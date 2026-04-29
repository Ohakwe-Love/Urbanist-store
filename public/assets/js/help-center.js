document.addEventListener('DOMContentLoaded', () => {
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach((item) => {
        const trigger = item.querySelector('.faq-question');

        if (!trigger) {
            return;
        }

        trigger.addEventListener('click', () => {
            const isActive = item.classList.contains('active');

            faqItems.forEach((faqItem) => faqItem.classList.remove('active'));

            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
});
