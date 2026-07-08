"""
BrainsBond007 Face Match Microservice
======================================
Flask-based API that compares face images using the face_recognition library.
Accepts base64-encoded images and returns match status with confidence score.

Usage:
    python face_match_server.py
    # Runs on http://0.0.0.0:5000
"""

from flask import Flask, request, jsonify
import face_recognition
import base64
import numpy as np
from io import BytesIO
from PIL import Image
import logging

app = Flask(__name__)
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger('FaceMatch')


def decode_image(b64_string):
    """Decode a base64-encoded image string to a numpy array for face_recognition."""
    try:
        image_bytes = base64.b64decode(b64_string)
        image = Image.open(BytesIO(image_bytes)).convert('RGB')
        return np.array(image)
    except Exception as e:
        logger.error(f"Failed to decode image: {e}")
        return None


@app.route('/health', methods=['GET'])
def health():
    """Health check endpoint."""
    return jsonify({'status': 'ok', 'service': 'brainsbond007-face-match'})


@app.route('/compare-faces', methods=['POST'])
def compare_faces():
    """
    Compare two face images.

    Request JSON:
        - stored_image: base64-encoded stored reference image
        - received_image: base64-encoded captured image to compare

    Response JSON:
        - match: boolean indicating if faces match
        - confidence: float (0-1) where lower = more similar
        - error: string if an error occurred
    """
    data = request.json

    if not data or 'stored_image' not in data or 'received_image' not in data:
        return jsonify({
            'match': False,
            'error': 'Missing stored_image or received_image in request body.',
        }), 400

    # Decode images
    stored_array = decode_image(data['stored_image'])
    received_array = decode_image(data['received_image'])

    if stored_array is None:
        return jsonify({
            'match': False,
            'error': 'Failed to decode stored_image.',
        }), 400

    if received_array is None:
        return jsonify({
            'match': False,
            'error': 'Failed to decode received_image.',
        }), 400

    # Extract face encodings
    try:
        stored_encodings = face_recognition.face_encodings(stored_array)
        received_encodings = face_recognition.face_encodings(received_array)
    except Exception as e:
        logger.error(f"Encoding error: {e}")
        return jsonify({
            'match': False,
            'error': f'Face encoding failed: {str(e)}',
        }), 500

    if len(stored_encodings) == 0:
        return jsonify({
            'match': False,
            'error': 'No face detected in stored image.',
        }), 400

    if len(received_encodings) == 0:
        return jsonify({
            'match': False,
            'error': 'No face detected in received image.',
        }), 400

    # Compare faces
    stored_encoding = stored_encodings[0]
    received_encoding = received_encodings[0]

    # face_distance returns a numpy array of distances (lower = more similar)
    distance = face_recognition.face_distance([stored_encoding], received_encoding)[0]

    # Tolerance of 0.6 is the default; lower distance means closer match
    # Convert distance to a confidence percentage (0-100)
    confidence = round((1.0 - distance) * 100, 2)
    is_match = distance <= 0.6  # ~70% confidence threshold

    logger.info(f"Face comparison: distance={distance:.4f}, confidence={confidence}%, match={is_match}")

    return jsonify({
        'match': bool(is_match),
        'confidence': confidence,
        'distance': round(float(distance), 4),
    })


if __name__ == '__main__':
    logger.info("Starting BrainsBond007 Face Match Service on port 5000...")
    app.run(host='0.0.0.0', port=5000, debug=False)
