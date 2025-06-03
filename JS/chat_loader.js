//chat_loader.js
fetch('/PT/HTML/chat_widget.html')
  .then(res => res.text())
  .then(html => {
    const div = document.createElement('div');
    div.innerHTML = html;
    document.body.appendChild(div);

    // Load and bind chat script AFTER widget is injected
    const script = document.createElement('script');
    script.src = '/PT/JS/chat_widget.js';
    script.onload = () => {
      initChatEvents();
    };
    document.body.appendChild(script);
  });
