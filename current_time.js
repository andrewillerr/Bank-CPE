function updateTime() {
    const now = new Date();
    const options = {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    };
    document.getElementById('current-time').innerText = now.toLocaleDateString('en-US', options);
}

setInterval(updateTime, 1000);
window.onload = updateTime;
