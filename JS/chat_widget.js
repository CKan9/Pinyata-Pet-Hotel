//chat_widget.js
function toggleChat() {
    const body = document.getElementById('chat-body');
    if (body) body.style.display = body.style.display === 'none' ? 'block' : 'none';
  }
  
  async function sendMessage() {
    try {
      const input = document.getElementById('chat-input');
      const messages = document.getElementById('chat-messages');
      const userMessage = input.value.trim();
      if (!userMessage) return;
  
      messages.innerHTML += `<div><strong>You:</strong> ${userMessage}</div>`;
      input.value = '';
  
      const response = await fetch('/PT/PHP/chat_bot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: userMessage })
      });
  
      const data = await response.json();
let botReply = data.reply;
if (data.debug_http || data.debug_response) {
  botReply += `<br><small style="color:gray">[HTTP ${data.debug_http}] ${data.debug_response}</small>`;
}
messages.innerHTML += `<div><strong>AI:</strong> ${botReply}</div>`;

      messages.scrollTop = messages.scrollHeight;
    } catch (error) {
      console.error('Chat error:', error);
      messages.innerHTML += `<div><strong>AI:</strong> Sorry, something went wrong.</div>`;
    }
  }
  
  
  // Bind click events after DOM is updated
  function initChatEvents() {
    const header = document.getElementById('chat-header');
    const sendBtn = document.getElementById('chat-send');
    if (header) header.onclick = toggleChat;
    if (sendBtn) sendBtn.onclick = sendMessage;
  }
  