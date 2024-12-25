import os
import openai
from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

# Initialize OpenAI client with GLHF configuration
client = openai.OpenAI(
    api_key="glhf_18e74141e8dbbf0609d964a189fc33b0",
    base_url="https://glhf.chat/api/openai/v1"
)

SYSTEM_PROMPT = """You are an agricultural expert assistant. You help farmers with:
1. Crop recommendations based on soil and weather conditions
2. Best farming practices and techniques
3. Disease identification and treatment in crops
4. Market prices and trends
5. Government schemes and policies for farmers
Provide clear, practical advice that farmers can implement."""

@app.route('/chat', methods=['POST'])
def chat():
    try:
        data = request.json
        user_message = data.get('message', '')
        
        completion = client.chat.completions.create(
            model="hf:mistralai/Mistral-7B-Instruct-v0.3",
            messages=[
                {"role": "system", "content": SYSTEM_PROMPT},
                {"role": "user", "content": user_message}
            ],
            stream=True  # Enable streaming to prevent timeouts
        )
        
        # Collect the streamed response
        full_response = ""
        for chunk in completion:
            if chunk.choices[0].delta.content is not None:
                full_response += chunk.choices[0].delta.content
        
        return jsonify({"response": full_response})
    
    except Exception as e:
        print(f"Error: {str(e)}")  # Log the error
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(port=5000)
