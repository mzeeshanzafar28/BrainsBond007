"""
BrainsBond007 Desktop Agent
============================
Employee-side monitoring client that runs on the employee's machine.
Handles: face verification, location check, screenshots, webcam captures,
heartbeat pings, and WebSocket command listening.

Usage:
    python exe_code.py --server https://your-server.com --admin-id 1 --email employee@example.com
"""

import requests
import time
import json
import cv2
import random
import datetime
import threading
import argparse
import logging
import sys
import os
from io import BytesIO
from PIL import ImageGrab

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] %(message)s',
    handlers=[
        logging.StreamHandler(sys.stdout),
        logging.FileHandler('brainsbond_agent.log', mode='a'),
    ]
)
logger = logging.getLogger('BrainsBond007')


class BrainsBondAgent:
    """Main agent class that orchestrates all monitoring functions."""

    def __init__(self, base_url, admin_id, employee_email):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api/agent"
        self.admin_id = admin_id
        self.employee_email = employee_email
        self.token = None
        self.employee_id = None
        self.session_id = None
        self.working_hours = None
        self.running = False
        self.headers = {}

    # ──────────────────────────────────────────────────────────
    # Authentication
    # ──────────────────────────────────────────────────────────
    def authenticate(self):
        """Authenticate with the server and obtain a Sanctum token."""
        logger.info("Authenticating with server...")
        try:
            response = requests.post(f"{self.base_url}/api/agent/login", json={
                'admin_id': self.admin_id,
                'employee_email': self.employee_email,
            }, timeout=15)

            if response.status_code == 200:
                data = response.json()
                self.token = data['token']
                self.employee_id = data['employee']['id']
                self.working_hours = {
                    'start': data['employee']['start_working_hour'],
                    'end': data['employee']['end_working_hour'],
                }
                self.headers = {
                    'Authorization': f'Bearer {self.token}',
                    'Accept': 'application/json',
                }
                logger.info(f"Authenticated as employee #{self.employee_id} ({data['employee']['name']})")
                return True
            else:
                logger.error(f"Authentication failed: {response.status_code} - {response.text}")
                return False
        except requests.exceptions.RequestException as e:
            logger.error(f"Connection error during authentication: {e}")
            return False

    # ──────────────────────────────────────────────────────────
    # Working Hours Check
    # ──────────────────────────────────────────────────────────
    def is_within_working_hours(self):
        """Check if the current time is within the employee's working hours."""
        if not self.working_hours:
            return False

        try:
            start = datetime.datetime.strptime(self.working_hours['start'], '%H:%M:%S').time()
            end = datetime.datetime.strptime(self.working_hours['end'], '%H:%M:%S').time()
            now = datetime.datetime.now().time()
            return start <= now <= end
        except (ValueError, TypeError):
            logger.warning("Could not parse working hours, assuming within hours.")
            return True

    # ──────────────────────────────────────────────────────────
    # Location Verification
    # ──────────────────────────────────────────────────────────
    def verify_location(self):
        """Verify employee location against allowed locations."""
        logger.info("Verifying location...")
        try:
            # Get approximate location via IP geolocation (fallback)
            # In production, use GPS or browser Geolocation API
            geo_response = requests.get('https://ipinfo.io/json', timeout=10)
            if geo_response.status_code == 200:
                geo_data = geo_response.json()
                loc = geo_data.get('loc', '0,0').split(',')
                lat, lng = float(loc[0]), float(loc[1])
            else:
                logger.warning("Could not determine location via IP, using defaults.")
                lat, lng = 0.0, 0.0

            response = requests.post(f"{self.api_url}/verify-location", json={
                'employee_id': self.employee_id,
                'latitude': lat,
                'longitude': lng,
            }, headers=self.headers, timeout=15)

            result = response.json()
            verified = result.get('status', False)
            if verified:
                logger.info("Location verified successfully.")
            else:
                logger.warning("Location outside allowed range. Admin will be notified.")
            return verified

        except requests.exceptions.RequestException as e:
            logger.error(f"Location verification failed: {e}")
            return False

    # ──────────────────────────────────────────────────────────
    # Face Verification
    # ──────────────────────────────────────────────────────────
    def verify_face(self):
        """Capture a face image and verify against stored images."""
        logger.info("Starting face verification...")
        try:
            cap = cv2.VideoCapture(0)
            if not cap.isOpened():
                logger.error("Could not access camera for face verification.")
                return False

            # Wait a moment for camera to warm up
            time.sleep(1)
            ret, frame = cap.read()
            cap.release()

            if not ret or frame is None:
                logger.error("Failed to capture image from camera.")
                return False

            # Encode frame as JPEG
            _, buffer = cv2.imencode('.jpg', frame)
            import base64
            image_b64 = base64.b64encode(buffer).decode('utf-8')

            response = requests.post(f"{self.api_url}/verify-face", json={
                'admin_id': self.admin_id,
                'image': image_b64,
            }, headers=self.headers, timeout=30)

            if response.status_code == 200:
                data = response.json()
                matched_id = data.get('employee_id')
                if matched_id == self.employee_id:
                    logger.info("Face verified successfully.")
                    return True
                else:
                    logger.warning(f"Face matched to different employee (#{matched_id}).")
                    return False
            else:
                logger.warning(f"Face verification failed: {response.status_code}")
                return False

        except Exception as e:
            logger.error(f"Face verification error: {e}")
            return False

    # ──────────────────────────────────────────────────────────
    # Session Management
    # ──────────────────────────────────────────────────────────
    def start_session(self, face_verified=False, location_verified=False):
        """Start a work session on the server."""
        logger.info("Starting work session...")
        try:
            response = requests.post(f"{self.api_url}/session/start", json={
                'employee_id': self.employee_id,
                'face_verified': face_verified,
                'location_verified': location_verified,
            }, headers=self.headers, timeout=15)

            if response.status_code == 201:
                data = response.json()
                self.session_id = data['session']['id']
                logger.info(f"Work session started (ID: {self.session_id})")
                return True
            elif response.status_code == 409:
                # Already has an active session
                data = response.json()
                self.session_id = data.get('session', {}).get('id')
                logger.info(f"Resuming existing session (ID: {self.session_id})")
                return True
            else:
                logger.error(f"Failed to start session: {response.status_code} - {response.text}")
                return False
        except requests.exceptions.RequestException as e:
            logger.error(f"Session start error: {e}")
            return False

    def end_session(self):
        """End the current work session."""
        if not self.session_id:
            return

        logger.info("Ending work session...")
        try:
            face_ok = self.verify_face()
            response = requests.post(f"{self.api_url}/session/end", json={
                'employee_id': self.employee_id,
                'session_id': self.session_id,
                'face_verified': face_ok,
            }, headers=self.headers, timeout=15)

            if response.status_code == 200:
                logger.info("Work session ended successfully.")
            else:
                logger.warning(f"Session end returned: {response.status_code}")
        except requests.exceptions.RequestException as e:
            logger.error(f"Session end error: {e}")
        finally:
            self.session_id = None

    # ──────────────────────────────────────────────────────────
    # Heartbeat (runs in background thread)
    # ──────────────────────────────────────────────────────────
    def heartbeat_loop(self):
        """Send periodic heartbeat pings to track uptime."""
        while self.running and self.session_id:
            try:
                response = requests.post(f"{self.api_url}/session/heartbeat", json={
                    'employee_id': self.employee_id,
                    'session_id': self.session_id,
                    'is_active': True,  # TODO: detect actual mouse/keyboard activity
                }, headers=self.headers, timeout=10)

                if response.status_code == 200:
                    logger.debug("Heartbeat sent.")
                else:
                    logger.warning(f"Heartbeat returned: {response.status_code}")
            except requests.exceptions.RequestException as e:
                logger.warning(f"Heartbeat failed: {e}")

            time.sleep(60)  # Heartbeat every 60 seconds

    # ──────────────────────────────────────────────────────────
    # Screenshot Capture (runs in background thread)
    # ──────────────────────────────────────────────────────────
    def screenshot_loop(self):
        """Capture and upload screenshots at random intervals."""
        while self.running and self.session_id:
            if not self.is_within_working_hours():
                time.sleep(60)
                continue

            # Random interval between 10-30 minutes
            wait_time = random.randint(600, 1800)
            logger.info(f"Next screenshot in {wait_time // 60} minutes.")

            # Sleep in small increments so we can stop quickly
            for _ in range(wait_time):
                if not self.running:
                    return
                time.sleep(1)

            try:
                screenshot = ImageGrab.grab()
                temp_path = 'screenshot_temp.jpg'
                screenshot.save(temp_path, 'JPEG', quality=70)

                with open(temp_path, 'rb') as img_file:
                    response = requests.post(
                        f"{self.api_url}/screenshots",
                        data={
                            'employee_id': self.employee_id,
                            'session_id': self.session_id,
                        },
                        files={'screenshot': ('screenshot.jpg', img_file, 'image/jpeg')},
                        headers=self.headers,
                        timeout=30,
                    )

                if response.status_code == 201:
                    logger.info("Screenshot uploaded successfully.")
                else:
                    logger.warning(f"Screenshot upload returned: {response.status_code}")

                # Clean up temp file
                if os.path.exists(temp_path):
                    os.remove(temp_path)

            except Exception as e:
                logger.error(f"Screenshot capture/upload error: {e}")

    # ──────────────────────────────────────────────────────────
    # Webcam Capture (runs in background thread)
    # ──────────────────────────────────────────────────────────
    def webcam_capture_loop(self):
        """Capture webcam images at random intervals (5 per session)."""
        captures_done = 0
        max_captures = 5

        while self.running and self.session_id and captures_done < max_captures:
            if not self.is_within_working_hours():
                time.sleep(60)
                continue

            wait_time = random.randint(1200, 3600)  # 20-60 minutes
            for _ in range(wait_time):
                if not self.running:
                    return
                time.sleep(1)

            try:
                cap = cv2.VideoCapture(0)
                if not cap.isOpened():
                    logger.warning("Camera not available for webcam capture.")
                    continue

                time.sleep(0.5)
                ret, frame = cap.read()
                cap.release()

                if not ret or frame is None:
                    continue

                temp_path = 'webcam_temp.jpg'
                cv2.imwrite(temp_path, frame)

                with open(temp_path, 'rb') as img_file:
                    response = requests.post(
                        f"{self.api_url}/webcam-capture",
                        data={
                            'employee_id': self.employee_id,
                            'session_id': self.session_id,
                        },
                        files={'image': ('webcam.jpg', img_file, 'image/jpeg')},
                        headers=self.headers,
                        timeout=30,
                    )

                if response.status_code == 201:
                    captures_done += 1
                    logger.info(f"Webcam capture uploaded ({captures_done}/{max_captures}).")

                if os.path.exists(temp_path):
                    os.remove(temp_path)

            except Exception as e:
                logger.error(f"Webcam capture error: {e}")

    # ──────────────────────────────────────────────────────────
    # End-of-Day Monitor (runs in background thread)
    # ──────────────────────────────────────────────────────────
    def end_of_day_monitor(self):
        """Watch for end of working hours and prompt attendance."""
        while self.running and self.session_id:
            if not self.is_within_working_hours():
                logger.info("Working hours ended. Prompting end-of-day attendance.")
                self.end_session()
                self.running = False
                return
            time.sleep(60)

    # ──────────────────────────────────────────────────────────
    # Main Run Loop
    # ──────────────────────────────────────────────────────────
    def run(self):
        """Main entry point: authenticate, verify, start session, launch monitors."""
        logger.info("=" * 50)
        logger.info("BrainsBond007 Desktop Agent Starting")
        logger.info("=" * 50)

        # Step 1: Authenticate
        if not self.authenticate():
            logger.error("Authentication failed. Exiting.")
            return

        # Step 2: Check working hours
        if not self.is_within_working_hours():
            logger.info("Outside working hours. Agent will wait...")
            while not self.is_within_working_hours():
                time.sleep(60)

        # Step 3: Verify location
        location_ok = self.verify_location()

        # Step 4: Verify face
        face_ok = self.verify_face()

        # Step 5: Start session
        if not self.start_session(face_verified=face_ok, location_verified=location_ok):
            logger.error("Could not start session. Exiting.")
            return

        # Step 6: Launch background monitoring threads
        self.running = True
        threads = [
            threading.Thread(target=self.heartbeat_loop, name='Heartbeat', daemon=True),
            threading.Thread(target=self.screenshot_loop, name='Screenshots', daemon=True),
            threading.Thread(target=self.webcam_capture_loop, name='WebcamCapture', daemon=True),
            threading.Thread(target=self.end_of_day_monitor, name='EndOfDay', daemon=True),
        ]

        for t in threads:
            t.start()
            logger.info(f"Started thread: {t.name}")

        # Keep main thread alive
        try:
            while self.running:
                time.sleep(1)
        except KeyboardInterrupt:
            logger.info("Agent interrupted by user.")
            self.running = False
            self.end_session()

        logger.info("BrainsBond007 Agent stopped.")


def main():
    parser = argparse.ArgumentParser(description='BrainsBond007 Desktop Agent')
    parser.add_argument('--server', required=True, help='Server URL (e.g., https://your-server.com)')
    parser.add_argument('--admin-id', required=True, type=int, help='Admin/Organization ID')
    parser.add_argument('--email', required=True, help='Employee email address')
    args = parser.parse_args()

    agent = BrainsBondAgent(
        base_url=args.server,
        admin_id=args.admin_id,
        employee_email=args.email,
    )
    agent.run()


if __name__ == "__main__":
    main()