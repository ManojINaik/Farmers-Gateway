from flask import Flask, request, jsonify, Response
from flask_cors import CORS
from openai import OpenAI
import json

app = Flask(__name__)
CORS(app)

# Initialize GLHF client
client = OpenAI(
    api_key="glhf_18e74141e8dbbf0609d964a189fc33b0",
    base_url="https://glhf.chat/api/openai/v1"
)

SYSTEM_PROMPT = """You are an expert agricultural assistant specializing in Indian farming practices. Your role is to:
1. Provide practical farming advice and crop management tips
2. Help identify plant diseases and suggest treatments
3. Recommend suitable crops based on seasons and conditions
4. Share modern farming techniques and best practices
5. Provide market insights and pricing information
6. Guide farmers about government schemes and subsidies
7. Offer sustainable and organic farming solutions
8. Assist with water management and irrigation techniques

Keep responses focused on agricultural topics and provide actionable advice that Indian farmers can implement."""

@app.route('/chat', methods=['POST'])
def chat():
    try:
        data = request.json
        user_message = data.get('message', '')
        
        if not user_message:
            return jsonify({'error': 'No message provided'}), 400

        # Create chat completion
        completion = client.chat.completions.create(
            model="hf:mistralai/Mistral-7B-Instruct-v0.3",
            messages=[
                {"role": "system", "content": SYSTEM_PROMPT},
                {"role": "user", "content": user_message}
            ],
            temperature=0.7,
            stream=False  # Disable streaming for now
        )

        # Extract response
        response = completion.choices[0].message.content
        return jsonify({'response': response})

    except Exception as e:
        print(f"Error in chat endpoint: {str(e)}")
        return jsonify({
            'error': 'An error occurred while processing your request',
            'details': str(e)
        }), 500

@app.route('/health', methods=['GET'])
def health_check():
    try:
        # Test the GLHF API connection
        test_response = client.chat.completions.create(
            model="hf:mistralai/Mistral-7B-Instruct-v0.3",
            messages=[{"role": "user", "content": "test"}],
            max_tokens=5
        )
        return jsonify({
            'status': 'healthy',
            'api_status': 'connected'
        })
    except Exception as e:
        return jsonify({
            'status': 'unhealthy',
            'error': str(e)
        }), 500

if __name__ == '__main__':
    print("🚀 Starting Agricultural AI Assistant server...")
    print("📡 GLHF API configured")
    print("💻 Server running at http://localhost:5000")
    app.run(debug=True, port=5000)
