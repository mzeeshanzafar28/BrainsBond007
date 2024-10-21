import requests
import time
import json
import cv2  # For camera access and face ID
import websocket
import random
import datetime
from pynput import keyboard  # For keystrokes
from PIL import ImageGrab  # For screenshots

# Define the base URL for API endpoints
BASE_URL = "https://your_laravel_site_endpoint"

# Function 1: Check Working Hours
def check_working_hours():
    response = requests.post(f"{BASE_URL}/get_working_hours")
    working_hours = response.json()
    
    start_working_hour = datetime.datetime.strptime(working_hours['start_working_hour'], '%H:%M:%S').time()
    end_working_hour = datetime.datetime.strptime(working_hours['end_working_hour'], '%H:%M:%S').time()
    current_time = datetime.datetime.now().time()

    if not (start_working_hour <= current_time <= end_working_hour):
        print("Working hours not started yet.")
        exit()
    else:
        print("Working hours confirmed.")
        return True

# Function 2: Verify Location
def verify_location():
    # Assuming we have a way to get current GPS coordinates
    current_location = {'lat': 00.00, 'lng': 00.00}  # Example lat/lng
    
    response = requests.post(f"{BASE_URL}/verify_location", json=current_location)
    if response.json() is True:
        print("Location verified.")
    else:
        print("You are outside of allowed location, this action will be reported to your Admin.")
    return True

# Function 3: Verify Face
def verify_face():
    # Capture an image from the device's camera
    cap = cv2.VideoCapture(0)
    ret, frame = cap.read()
    
    # Save the captured image to send
    cv2.imwrite('face.jpg', frame)
    cap.release()

    with open('face.jpg', 'rb') as img:
        response = requests.post(f"{BASE_URL}/verify_face", files={'image': img})
    
    if response.json() is True:
        print("Face verified.")
        return True
    else:
        print("Face mismatch. Proceeding to ID verification.")
        return verify_id()

# Function 4: Verify ID
def verify_id():
    # Prompt user to scan ID card via camera
    print("Please scan your ID card.")
    
    cap = cv2.VideoCapture(0)
    ret, frame = cap.read()
    
    # Save the captured image of ID card
    cv2.imwrite('id_card.jpg', frame)
    cap.release()

    with open('id_card.jpg', 'rb') as img:
        response = requests.post(f"{BASE_URL}/verify_id", files={'id_image': img})
    
    if response.json() is True:
        print("ID verified.")
        return True
    else:
        print("Face and ID mismatch.")
        exit()

# Function 5: Start Session
def start_session():
    current_time = datetime.datetime.now().isoformat()
    requests.post(f"{BASE_URL}/session_started", json={'time': current_time})
    
    # Start screencasting, keystrokes, and screenshots concurrently
    screencast()
    keystrokes()
    send_screenshots()

# Function 6: Screencast
def screencast():
    ws = websocket.create_connection(f"{BASE_URL}/ws_screencast")
    while True:
        # Screencasting logic (e.g., sending screen frames)
        pass

# Function 7: Keystrokes
def keystrokes():
    def on_press(key):
        try:
            # Send keystrokes over websocket
            ws = websocket.create_connection(f"{BASE_URL}/ws_keystrokes")
            ws.send(f'{key.char}')
        except AttributeError:
            pass
    
    with keyboard.Listener(on_press=on_press) as listener:
        listener.join()

# Function 8: Send Screenshots
def send_screenshots():
    while True:
        if check_working_hours():
            time.sleep(random.randint(600, 1800))  # Random interval between screenshots
            screenshot = ImageGrab.grab()
            screenshot.save("screenshot.jpg")
            with open("screenshot.jpg", 'rb') as img:
                requests.post(f"{BASE_URL}/save_screenshots", files={'screenshot': img})

# Function 9: Seize System
def seize_system():
    ws = websocket.create_connection(f"{BASE_URL}/ws_seize_system")
    while True:
        # Logic to seize the system if instructed
        pass

# Function 10: End Session
def end_session():
    while True:
        current_time = datetime.datetime.now().time()
        response = requests.post(f"{BASE_URL}/get_working_hours")
        working_hours = response.json()
        end_working_hour = datetime.datetime.strptime(working_hours['end_working_hour'], '%H:%M:%S').time()

        if current_time >= end_working_hour:
            print("Working hours ended, please mark your attendance by scanning your face.")
            verify_location()
            if verify_face():
                requests.post(f"{BASE_URL}/session_ended", json={'time': current_time.isoformat()})
            else:
                requests.post(f"{BASE_URL}/session_ended", json={'time': None})
            break
        time.sleep(60)  # Check every minute
