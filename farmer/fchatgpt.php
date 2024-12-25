<?php
include ('fsession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
header("location: ../index.php");} // Redirecting To Home Page
$query4 = "SELECT * from farmerlogin where email='$user_check'";
              $ses_sq4 = mysqli_query($conn, $query4);
              $row4 = mysqli_fetch_assoc($ses_sq4);
              $para1 = $row4['farmer_id'];
              $para2 = $row4['farmer_name'];

// Load environment variables
$envFile = __DIR__ . '/../.env';
$envVariables = parse_ini_file($envFile);
$aiApiUrl = $envVariables['AI_API_URL'];
?>
<!DOCTYPE html>
<html>
<?php require ('fheader.php');  ?>
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</head>
<style>
:root {
    --primary-color: #2C5F2D;
    --secondary-color: #97BC62;
    --accent-color: #DAE5D0;
    --text-color: #1A1A1A;
    --light-color: #F5F5F5;
    --success-color: #2ECC71;
    --info-color: #3498DB;
    --danger-color: #E74C3C;
}

.chat-container {
    max-width: 1000px;
    margin: 2rem auto;
    background: white;
    border-radius: 20px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    overflow: hidden;
}

.chat-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.chat-header h1 {
    margin: 0;
    color: white;
    font-size: 1.8rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.chat-header h1 i {
    font-size: 2rem;
    background: rgba(255,255,255,0.2);
    padding: 0.5rem;
    border-radius: 12px;
}

.header-buttons {
    display: flex;
    gap: 0.8rem;
}

.header-button {
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    border: none;
    color: white;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
}

.print-btn {
    background-color: var(--info-color);
}

.clear-btn {
    background-color: var(--danger-color);
}

.header-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}

.chat-box {
    height: calc(100vh - 300px);
    min-height: 400px;
    padding: 2rem;
    overflow-y: auto;
    background: #FFFFFF;
    scroll-behavior: smooth;
}

.message {
    max-width: 75%;
    margin-bottom: 2rem;
    position: relative;
    clear: both;
    font-size: 1rem;
    line-height: 1.6;
}

.message-content {
    padding: 1.2rem 1.5rem;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: relative;
}

.left-side .message-content {
    background-color: var(--accent-color);
    color: var(--text-color);
    border-bottom-left-radius: 5px;
}

.right-side .message-content {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-bottom-right-radius: 5px;
}

.message-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
    color: #666;
}

.message-info i {
    font-size: 0.8rem;
}

.left-side {
    float: left;
}

.right-side {
    float: right;
}

.chat-footer {
    background: white;
    padding: 1.5rem;
    border-top: 1px solid var(--accent-color);
}

.input-group {
    display: flex;
    gap: 1rem;
    align-items: center;
    background: var(--light-color);
    padding: 0.5rem;
    border-radius: 15px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.input-group:focus-within {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 3px rgba(151, 188, 98, 0.2);
}

#userInput {
    flex: 1;
    border: none;
    background: none;
    padding: 0.8rem;
    font-size: 1rem;
    color: var(--text-color);
}

#userInput:focus {
    outline: none;
}

#sendButton {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
}

#sendButton:not(:disabled):hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
}

#sendButton:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.typing-indicator {
    display: none;
    padding: 1rem;
    margin: 1rem 0;
    color: #666;
    font-style: italic;
    background: rgba(0,0,0,0.03);
    border-radius: 10px;
    width: fit-content;
}

.typing-dots {
    display: inline-flex;
    gap: 3px;
    margin-left: 5px;
}

.typing-dot {
    width: 6px;
    height: 6px;
    background: #666;
    border-radius: 50%;
    animation: typingAnimation 1.4s infinite;
}

.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingAnimation {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
}

/* Custom Scrollbar */
.chat-box::-webkit-scrollbar {
    width: 6px;
}

.chat-box::-webkit-scrollbar-track {
    background: var(--light-color);
}

.chat-box::-webkit-scrollbar-thumb {
    background: var(--secondary-color);
    border-radius: 10px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .chat-container {
        margin: 1rem;
        border-radius: 15px;
    }

    .chat-header h1 {
        font-size: 1.4rem;
    }

    .header-button {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }

    .message {
        max-width: 85%;
    }

    .chat-box {
        height: calc(100vh - 250px);
        padding: 1rem;
    }
}
</style>

<body class="bg-white" id="top">
<?php include ('fnav.php'); ?>

<div class="chat-container">
    <div class="chat-header">
        <h1>
            <i class="fas fa-robot"></i>
            Agricultural Assistant
        </h1>
        <div class="header-buttons">
            <button class="header-button print-btn" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
            <button class="header-button clear-btn" onclick="clearContent()">
                <i class="fas fa-trash"></i> Clear
            </button>
        </div>
    </div>

    <div class="chat-box" id="chatbox">
        <div class="message left-side">
            <div class="message-info">
                <i class="fas fa-robot"></i>
                <span>Assistant</span>
                <span class="message-time">Just now</span>
            </div>
            <div class="message-content">
                Hello! I'm your agricultural assistant. I can help you with crop recommendations, farming practices, disease identification, market prices, and government schemes. How can I assist you today?
            </div>
        </div>
        <div class="typing-indicator" id="typingIndicator">
            <i class="fas fa-robot"></i> Assistant is thinking
            <div class="typing-dots">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>
    </div>

    <div class="chat-footer">
        <div class="input-group">
            <input id="userInput" type="text" 
                   placeholder="Type your farming question here..."
                   autocomplete="off" />
            <button id="sendButton" type="button">
                <i class="fas fa-paper-plane"></i>
                Send
            </button>
        </div>
    </div>
</div>

<?php require("../modern-footer.php");?>

<script>
async function generateAIResponse(prompt) {
    try {
        console.log('Sending request to proxy');
        const response = await fetch('ai_proxy.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                prompt: prompt,
                temperature: 0.7,
                max_length: 200
            })
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server response:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Response data:', data);

        // Process the response to extract the relevant text
        let processedResponse = '';
        if (data.response) {
            const fullText = data.response;
            
            // Get text before "### Instruction"
            const beforeInstruction = fullText.split('### Instruction')[0].trim();
            
            // Get text after "### Output:"
            const afterOutput = fullText.split('### Output:')[1];
            
            if (beforeInstruction) {
                processedResponse = beforeInstruction;
            }
            if (afterOutput) {
                processedResponse = afterOutput.trim();
            }
            
            // If both parts exist, use the appropriate one based on response structure
            if (beforeInstruction && afterOutput) {
                processedResponse = afterOutput.trim();
            }
        }

        return { response: processedResponse || data.response };
    } catch (error) {
        console.error('Detailed error:', error);
        throw error;
    }
}

function appendMessage(message, isUser) {
    const chatbox = document.getElementById('chatbox');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${isUser ? 'right-side' : 'left-side'}`;
    const messageInfo = document.createElement('div');
    messageInfo.className = 'message-info';
    messageInfo.innerHTML = `<span class="sender">${isUser ? 'You' : 'AI Assistant'}</span>`;
    
    const messageContent = document.createElement('div');
    messageContent.className = 'message-content';
    messageContent.textContent = message;
    
    messageDiv.appendChild(messageInfo);
    messageDiv.appendChild(messageContent);
    chatbox.appendChild(messageDiv);
    chatbox.scrollTop = chatbox.scrollHeight;
}

document.getElementById('sendButton').addEventListener('click', async function() {
    const input = document.getElementById('userInput');
    const message = input.value.trim();
    
    if (message) {
        // Show user message
        appendMessage(message, true);
        input.value = '';
        
        // Show typing indicator
        const typingIndicator = document.createElement('div');
        typingIndicator.className = 'message left-side typing-indicator';
        typingIndicator.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';
        document.getElementById('chatbox').appendChild(typingIndicator);
        
        try {
            // Generate AI response
            const response = await generateAIResponse(message);
            
            // Remove typing indicator
            typingIndicator.remove();
            
            // Show AI response
            if (response && response.response) {
                appendMessage(response.response, false);
            } else {
                appendMessage("I apologize, but I couldn't generate a response at this time.", false);
            }
        } catch (error) {
            // Remove typing indicator
            typingIndicator.remove();
            
            // Show error message
            appendMessage("I apologize, but there was an error connecting to the AI service. Please try again later.", false);
            console.error('Error:', error);
        }
    }
});

// Handle Enter key press
document.getElementById('userInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('sendButton').click();
    }
});
</script>

</body>
</html>
