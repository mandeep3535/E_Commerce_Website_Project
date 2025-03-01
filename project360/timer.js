document.addEventListener("DOMContentLoaded", function () {
    function startCountdown() {
        let now = new Date();
        let targetTime = new Date();
        targetTime.setHours(24, 0, 0, 0); //  reset at the next 24-hour mark

        function updateCountdown() {
            let now = new Date().getTime();
            let timeLeft = targetTime - now;

            if (timeLeft <= 0) {
                targetTime = new Date();
                targetTime.setHours(24, 0, 0, 0); // Reset to next 24-hour cycle
                timeLeft = targetTime - now;
            }

            let days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            let hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            document.getElementById("days").textContent = days;
            document.getElementById("hours").textContent = hours;
            document.getElementById("minutes").textContent = minutes;
            document.getElementById("seconds").textContent = seconds;
        }

        updateCountdown(); 
        setInterval(updateCountdown, 1000); // Update every second
    }

    startCountdown();
});
