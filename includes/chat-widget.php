<!-- Chat Widget Dependencies -->
<link rel="stylesheet" href="/agriculture-portal/assets/css/chat-widget.css">
<script src="/agriculture-portal/assets/js/chat-widget.js"></script>

<!-- Font Awesome if not already included -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Chat Widget HTML Structure -->
<div class="chat-widget">
    <div class="chat-button">
        <i class="fas fa-comments"></i>
    </div>
    <div class="chat-window">
        <div class="chat-header">
            <span>Agriculture Assistant</span>
            <span class="chat-close">&times;</span>
        </div>
        <div class="chat-messages">
            <div class="message bot">
                Hello! I'm your agriculture assistant. How can I help you today?
            </div>
            <div class="typing-indicator">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" placeholder="Type your message...">
            <button>Send</button>
        </div>
    </div>
</div>
