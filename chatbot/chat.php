<?php
include('../farmer/fsession.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agricultural Assistant</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .chat-container {
            height: 500px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .message {
            margin-bottom: 15px;
            padding: 12px 15px;
            border-radius: 15px;
            max-width: 80%;
            word-wrap: break-word;
        }
        .user-message {
            background-color: #28a745;
            color: white;
            margin-left: auto;
        }
        .bot-message {
            background-color: #ffffff;
            color: #333;
            border: 1px solid #dee2e6;
        }
        .typing-indicator {
            display: none;
            color: #666;
            font-style: italic;
            margin-bottom: 10px;
            padding-left: 10px;
        }
        .chat-header {
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 20px;
        }
        .chat-input {
            border-radius: 20px;
            padding: 10px 20px;
        }
        .send-button {
            border-radius: 20px;
            padding: 10px 25px;
            background-color: #28a745;
            border-color: #28a745;
        }
        .send-button:hover {
            background-color: #218838;
            border-color: #218838;
        }
    </style>
</head>
<body>
    <?php include('../farmer/fnav.php'); ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="chat-header">
                    <h2 class="text-center mb-0">Agricultural Assistant</h2>
                    <p class="text-center mb-0 mt-2">Your AI farming expert</p>
                </div>
                <div class="chat-container" id="chatContainer">
                    <div class="message bot-message">
                        Hello! I'm your agricultural assistant. I can help you with crop recommendations, farming practices, disease identification, market prices, and government schemes. How can I assist you today?
                    </div>
                </div>
                <div class="typing-indicator" id="typingIndicator">Assistant is thinking...</div>
                <div class="input-group">
                    <input type="text" class="form-control chat-input" id="messageInput" placeholder="Type your farming question here...">
                    <div class="input-group-append">
                        <button class="btn btn-primary send-button" onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script>
        let isProcessing = false;

        function appendMessage(message, isUser) {
            const chatContainer = document.getElementById('chatContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isUser ? 'user-message' : 'bot-message'}`;
            messageDiv.textContent = message;
            chatContainer.appendChild(messageDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function sendMessage() {
            if (isProcessing) return;
            
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (message === '') return;
            
            isProcessing = true;
            appendMessage(message, true);
            messageInput.value = '';
            
            document.getElementById('typingIndicator').style.display = 'block';
            
            fetch('chat_endpoint.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('typingIndicator').style.display = 'none';
                
                if (data.error) {
                    appendMessage('Sorry, I encountered an error. Please try again in a moment.', false);
                } else {
                    appendMessage(data.response, false);
                }
            })
            .catch(error => {
                document.getElementById('typingIndicator').style.display = 'none';
                appendMessage('Sorry, I encountered an error. Please try again in a moment.', false);
                console.error('Error:', error);
            })
            .finally(() => {
                isProcessing = false;
            });
        }

        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !isProcessing) {
                sendMessage();
            }
        });
    </script>
</body>
</html>
