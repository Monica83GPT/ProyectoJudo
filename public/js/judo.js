document.addEventListener("DOMContentLoaded", () => {
    const eventList = document.querySelector("#event-list");
    const scrollButton = document.querySelector(".event-scroll");

    if (!eventList || !scrollButton) return;

    let animationFrame = null;
    let previousTime = 0;

    function scrollWhileHovered(timestamp) {
        if (!previousTime) previousTime = timestamp;
        const elapsed = timestamp - previousTime;
        previousTime = timestamp;

        const maxScroll = eventList.scrollHeight - eventList.clientHeight;
        if (eventList.scrollTop >= maxScroll) {
            animationFrame = null;
            return;
        }

        eventList.scrollTop = Math.min(maxScroll, eventList.scrollTop + elapsed * 0.07);
        animationFrame = requestAnimationFrame(scrollWhileHovered);
    }

    function startScrolling() {
        if (animationFrame === null) {
            previousTime = 0;
            animationFrame = requestAnimationFrame(scrollWhileHovered);
        }
    }

    function stopScrolling() {
        if (animationFrame !== null) {
            cancelAnimationFrame(animationFrame);
            animationFrame = null;
        }
    }

    scrollButton.addEventListener("pointerenter", startScrolling);
    scrollButton.addEventListener("pointerleave", stopScrolling);
    scrollButton.addEventListener("focus", startScrolling);
    scrollButton.addEventListener("blur", stopScrolling);
    scrollButton.addEventListener("click", () => {
        stopScrolling();
        eventList.scrollBy({ top: eventList.clientHeight * 0.75, behavior: "smooth" });
    });
});