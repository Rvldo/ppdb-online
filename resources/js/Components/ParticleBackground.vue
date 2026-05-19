<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const canvas = ref(null);
let animationId = null;
let particles = [];

const PARTICLE_COUNT = 50;

class Particle {
    constructor(w, h) {
        this.reset(w, h);
    }
    reset(w, h) {
        this.x = Math.random() * w;
        this.y = Math.random() * h;
        this.size = Math.random() * 2 + 0.5;
        this.speedX = (Math.random() - 0.5) * 0.5;
        this.speedY = (Math.random() - 0.5) * 0.5;
        this.opacity = Math.random() * 0.5 + 0.1;
    }
    update(w, h) {
        this.x += this.speedX;
        this.y += this.speedY;
        if (this.x < 0 || this.x > w) this.speedX *= -1;
        if (this.y < 0 || this.y > h) this.speedY *= -1;
    }
    draw(ctx) {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(167, 139, 250, ${this.opacity})`;
        ctx.fill();
    }
}

function drawConnections(ctx, particles, maxDist = 120) {
    for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < particles.length; j++) {
            const dx = particles[i].x - particles[j].x;
            const dy = particles[i].y - particles[j].y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < maxDist) {
                ctx.beginPath();
                ctx.moveTo(particles[i].x, particles[i].y);
                ctx.lineTo(particles[j].x, particles[j].y);
                ctx.strokeStyle = `rgba(139, 92, 246, ${0.08 * (1 - dist / maxDist)})`;
                ctx.lineWidth = 0.5;
                ctx.stroke();
            }
        }
    }
}

onMounted(() => {
    const c = canvas.value;
    if (!c) return;
    const ctx = c.getContext('2d');

    function resize() {
        c.width = c.offsetWidth * (window.devicePixelRatio || 1);
        c.height = c.offsetHeight * (window.devicePixelRatio || 1);
        ctx.scale(window.devicePixelRatio || 1, window.devicePixelRatio || 1);
    }

    resize();

    const w = c.offsetWidth;
    const h = c.offsetHeight;
    particles = Array.from({ length: PARTICLE_COUNT }, () => new Particle(w, h));

    function animate() {
        ctx.clearRect(0, 0, c.offsetWidth, c.offsetHeight);
        particles.forEach(p => {
            p.update(c.offsetWidth, c.offsetHeight);
            p.draw(ctx);
        });
        drawConnections(ctx, particles);
        animationId = requestAnimationFrame(animate);
    }

    animate();
    window.addEventListener('resize', resize);
});

onUnmounted(() => {
    if (animationId) cancelAnimationFrame(animationId);
});
</script>

<template>
    <canvas ref="canvas" class="absolute inset-0 w-full h-full pointer-events-none" style="opacity: 0.6;"></canvas>
</template>
