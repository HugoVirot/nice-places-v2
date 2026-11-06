<template>
    <div class="loading-spinner" role="status" aria-live="polite">
        <span class="spinner" aria-hidden="true"></span>
        <span class="loading-text">Chargement en cours...</span>
    </div>
</template>

<style scoped>
.loading-spinner {
    display: flex;
    min-height: 60vh;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.spinner {
    width: 3.5rem;
    height: 3.5rem;
    border: 0.4rem solid #94D1BE;
    border-top-color: #1c6e8c;
    border-right-color: #1c6e8c;
    border-radius: 50%;
    animation: spinner-rotation 0.8s linear infinite;
}

.loading-text {
    color: #1c6e8c;
}

@keyframes spinner-rotation {
    to {
        transform: rotate(360deg);
    }
}

@media (prefers-reduced-motion: reduce) {
    .spinner {
        animation-duration: 2s;
    }
}
</style>
