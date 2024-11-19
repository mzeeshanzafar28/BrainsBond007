from flask import Flask, request, jsonify
import face_recognition
import base64
import numpy as np

app = Flask(__name__)

@app.route('/compare-faces', methods=['POST'])
def compare_faces():
    data = request.json
    stored_image = data['stored_image']
    received_image = data['received_image']

    # Decode base64 images
    stored_image = face_recognition.load_image_file(base64.b64decode(stored_image))
    received_image = face_recognition.load_image_file(base64.b64decode(received_image))

    # Get encodings
    stored_encoding = face_recognition.face_encodings(stored_image)[0]
    received_encoding = face_recognition.face_encodings(received_image)[0]

    # Compare faces
    results = face_recognition.compare_faces([stored_encoding], received_encoding)
    return jsonify({'match': results[0]})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
