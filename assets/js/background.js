document.addEventListener("mousemove", (event) => {
    const x = (event.clientX / window.innerWidth) * 100;
    const y = (event.clientY / window.innerHeight) * 100;

    document.body.style.background = `
        radial-gradient(circle at ${x}% ${y}%, rgba(252, 163, 17, 0.22), transparent 28%),
        radial-gradient(circle at 80% 10%, rgba(255, 255, 255, 0.08), transparent 25%),
        linear-gradient(135deg, #000000, #14213D)
    `;
});