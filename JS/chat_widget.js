let isChatOpen = false;

function toggleChat() {
    isChatOpen = !isChatOpen;
    const chatBody = document.getElementById('chat-body');
    const chatIcon = document.getElementById('chat-icon');
    
    if (chatBody && chatIcon) {
        chatBody.style.display = isChatOpen ? 'block' : 'none';
        chatIcon.style.display = isChatOpen ? 'none' : 'flex';
        
        if (isChatOpen) {
            setTimeout(() => {
                const input = document.getElementById('chat-input');
                if (input) input.focus();
            }, 100);
        }
    }
}

async function sendMessage() {
    const input = document.getElementById('chat-input');
    const messages = document.getElementById('chat-messages');
    const sendBtn = document.getElementById('chat-send');
    
    const userMessage = input.value.trim();
    if (!userMessage || !messages) return;

    // Add user message
    messages.innerHTML += `<div class="message user">${userMessage}</div>`;
    input.value = '';
    
    // Disable send button during request
    if (sendBtn) sendBtn.disabled = true;
    
    // Add loading indicator
    const loadingId = 'loading-' + Date.now();
    messages.innerHTML += `<div id="${loadingId}" class="message loading">Thinking...</div>`;
    messages.scrollTop = messages.scrollHeight;

    try {
        // Test endpoint - replace with your actual endpoint
        const response = await fetch('/PT/PHP/chat_bot.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: userMessage }),
            credentials: 'include'
        });

        if (!response) {
            throw new Error('No response from server');
        }

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Server error: ${response.status} - ${errorText || 'No details'}`);
        }

        const responseText = await response.text();
        if (!responseText) {
            throw new Error('Server returned empty response');
        }

        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            throw new Error(`Invalid JSON: ${e.message}`);
        }

        if (!data || typeof data !== 'object') {
            throw new Error('Invalid response format');
        }

        // Remove loading indicator
        const loadingElement = document.getElementById(loadingId);
        if (loadingElement) loadingElement.remove();
        
        // Add bot response
        if (data.reply) {
            messages.innerHTML += `<div class="message bot">${data.reply}</div>`;
        } else {
            throw new Error('No reply in response');
        }
        
    } catch (error) {
        console.error('Chat error:', error);
        const loadingElement = document.getElementById(loadingId);
        if (loadingElement) loadingElement.remove();
        
        let errorMessage = "Sorry, we're experiencing technical difficulties.";
        
        if (error.message.includes('JSON')) {
            errorMessage = "There was a problem understanding the server's response.";
        } else if (error.message.includes('CORS')) {
            errorMessage = "Connection blocked by security policies. Please refresh the page.";
        }
        
        messages.innerHTML += `<div class="message error">${errorMessage}</div>`;
    } finally {
        if (sendBtn) sendBtn.disabled = false;
        messages.scrollTop = messages.scrollHeight;
        if (input) input.focus();
    }
}

function initChatEvents() {
    const chatIcon = document.getElementById('chat-icon');
    const chatClose = document.getElementById('chat-close');
    const sendBtn = document.getElementById('chat-send');
    const input = document.getElementById('chat-input');
    
    if (chatIcon) {
        chatIcon.addEventListener('click', toggleChat);
    }
    
    if (chatClose) {
        chatClose.addEventListener('click', toggleChat);
    }
    
    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }
    
    if (input) {
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
    
    // Initialize welcome message if chat is empty
    const messages = document.getElementById('chat-messages');
    if (messages && messages.children.length === 0) {
        messages.innerHTML = `
            <div class="message bot">
                Hello! I'm your Pet Hotel Assistant. 🐾<br>
                How can I help you today?
            </div>
        `;
    }
}

// Initialize when DOM is ready
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setTimeout(initChatEvents, 0);
} else {
    document.addEventListener('DOMContentLoaded', initChatEvents);
}