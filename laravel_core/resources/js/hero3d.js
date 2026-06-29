const HERO_SELECTOR = '[data-hero-3d]';

function clamp(value, min, max) {
    return Math.min(max, Math.max(min, value));
}

function createPoint(radius, tilt) {
    const angle = Math.random() * Math.PI * 2;
    const y = (Math.random() - 0.5) * tilt;

    return {
        x: Math.cos(angle) * radius,
        y,
        z: Math.sin(angle) * radius,
        speed: 0.0014 + Math.random() * 0.0016,
        phase: Math.random() * Math.PI * 2,
        size: 0.9 + Math.random() * 1.4,
        alpha: 0.3 + Math.random() * 0.5,
    };
}

function randomBetween(min, max) {
    return min + Math.random() * (max - min);
}

function projectPoint(point, rotationY, rotationX, viewport) {
    const cosY = Math.cos(rotationY);
    const sinY = Math.sin(rotationY);

    const xzX = point.x * cosY - point.z * sinY;
    const xzZ = point.x * sinY + point.z * cosY;

    const cosX = Math.cos(rotationX);
    const sinX = Math.sin(rotationX);

    const yzY = point.y * cosX - xzZ * sinX;
    const yzZ = point.y * sinX + xzZ * cosX;

    const perspective = viewport.depth / (viewport.depth + yzZ + viewport.radius * 1.8);

    return {
        x: xzX * perspective + viewport.centerX,
        y: yzY * perspective + viewport.centerY,
        alpha: clamp(perspective * 0.95, 0.12, 1),
        scale: perspective,
        z: yzZ,
    };
}

function initHero3D() {
    const hero = document.querySelector(HERO_SELECTOR);
    if (!hero) {
        return;
    }

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const coarsePointer = window.matchMedia('(pointer: coarse)').matches;

    if (reduceMotion) {
        hero.classList.add('hero-3d-disabled');
        return;
    }

    const canvas = hero.querySelector('canvas');
    if (!canvas) {
        return;
    }

    const context = canvas.getContext('2d', { alpha: true, desynchronized: true });
    if (!context) {
        return;
    }

    const palette = {
        stroke: '24, 130, 255',
        neon: '57, 244, 255',
        pink: '255, 79, 216',
        secondary: '255, 255, 255',
    };

    const state = {
        running: true,
        inView: true,
        width: 0,
        height: 0,
        dpr: 1,
        radius: 140,
        depth: 520,
        rotationY: 0,
        rotationX: -0.32,
        pointerX: 0,
        pointerY: 0,
        pointerEnergy: 0,
        targetEnergy: 0,
        lastPointerX: null,
        lastPointerY: null,
        points: [],
        rings: [],
        bursts: [],
        fireworks: [],
        maxBursts: coarsePointer ? 2 : 4,
        maxFireworks: coarsePointer ? 5 : 10,
        maxLinks: coarsePointer ? 28 : 90,
        lastAutoFireworkAt: 0,
        nextAutoFireworkDelay: 900,
        frameId: null,
        observer: null,
    };

    function rebuildScene() {
        const minSide = Math.min(state.width, state.height);

        state.radius = clamp(minSide * 0.24, 120, 230);
        state.depth = state.radius * 3;

        const pointCount = coarsePointer ? 28 : 52;
        const ringCount = coarsePointer ? 4 : 7;

        state.points = Array.from({ length: pointCount }, () => createPoint(state.radius, state.radius * 0.7));
        state.rings = Array.from({ length: ringCount }, (_, index) => {
            const progress = (index + 1) / (ringCount + 1);
            return {
                radius: state.radius * (0.45 + progress * 0.8),
                y: (progress - 0.5) * state.radius * 1.1,
                wobble: Math.random() * Math.PI * 2,
            };
        });
    }

    function resize() {
        const rect = hero.getBoundingClientRect();

        state.width = Math.max(1, Math.floor(rect.width));
        state.height = Math.max(1, Math.floor(rect.height));
        state.dpr = Math.min(window.devicePixelRatio || 1, 1.5);

        canvas.width = Math.floor(state.width * state.dpr);
        canvas.height = Math.floor(state.height * state.dpr);
        canvas.style.width = `${state.width}px`;
        canvas.style.height = `${state.height}px`;

        context.setTransform(state.dpr, 0, 0, state.dpr, 0, 0);
        rebuildScene();
    }

    function drawRing(ring, time) {
        const segments = 70;
        const wobble = Math.sin(time * 0.00035 + ring.wobble) * 0.08;

        context.beginPath();
        for (let i = 0; i <= segments; i += 1) {
            const theta = (i / segments) * Math.PI * 2;
            const x = Math.cos(theta) * ring.radius;
            const z = Math.sin(theta) * ring.radius;

            const projected = projectPoint(
                { x, y: ring.y + Math.sin(theta * 2 + time * 0.0006) * ring.radius * wobble, z },
                state.rotationY,
                state.rotationX,
                {
                    centerX: state.width * 0.5,
                    centerY: state.height * 0.52,
                    depth: state.depth,
                    radius: state.radius,
                },
            );

            if (i === 0) {
                context.moveTo(projected.x, projected.y);
            } else {
                context.lineTo(projected.x, projected.y);
            }
        }

        const ringEnergy = 0.1 + state.pointerEnergy * 0.24;
        context.strokeStyle = `rgba(${palette.stroke}, ${ringEnergy})`;
        context.lineWidth = 1 + state.pointerEnergy * 0.6;
        context.stroke();
    }

    function projectAnimatedPoint(point, time) {
        point.phase += point.speed;
        const hover = Math.sin(point.phase + time * 0.0009) * 0.12;

        return projectPoint(
            {
                x: point.x * (1 + hover),
                y: point.y + Math.sin(time * 0.001 + point.phase) * 6,
                z: point.z,
            },
            state.rotationY,
            state.rotationX,
            {
                centerX: state.width * 0.5,
                centerY: state.height * 0.52,
                depth: state.depth,
                radius: state.radius,
            },
        );
    }

    function drawConnections(projectedPoints) {
        if (projectedPoints.length < 2) {
            return;
        }

        const maxDistance = coarsePointer ? 82 : 112;
        const maxDistanceSq = maxDistance * maxDistance;
        let linksDrawn = 0;

        for (let i = 0; i < projectedPoints.length && linksDrawn < state.maxLinks; i += 1) {
            const from = projectedPoints[i];

            for (let j = i + 1; j < projectedPoints.length && linksDrawn < state.maxLinks; j += 1) {
                const to = projectedPoints[j];
                const dx = from.projected.x - to.projected.x;
                const dy = from.projected.y - to.projected.y;
                const distSq = dx * dx + dy * dy;

                if (distSq > maxDistanceSq) {
                    continue;
                }

                const intensity = 1 - Math.sqrt(distSq) / maxDistance;
                const alpha = intensity * intensity * (0.06 + state.pointerEnergy * 0.22);

                context.beginPath();
                context.moveTo(from.projected.x, from.projected.y);
                context.lineTo(to.projected.x, to.projected.y);
                context.strokeStyle = `rgba(${palette.neon}, ${alpha})`;
                context.lineWidth = 0.5 + intensity * 1.2;
                context.stroke();

                linksDrawn += 1;
            }
        }
    }

    function drawPoint(point, projected) {
        const dotSize = point.size * projected.scale * 1.9;

        const glowSize = dotSize * (1.8 + state.pointerEnergy * 1.5);
        context.beginPath();
        context.arc(projected.x, projected.y, glowSize, 0, Math.PI * 2);
        context.fillStyle = `rgba(${palette.neon}, ${(point.alpha * projected.alpha * 0.14) + state.pointerEnergy * 0.05})`;
        context.fill();

        context.beginPath();
        context.arc(projected.x, projected.y, dotSize, 0, Math.PI * 2);
        context.fillStyle = `rgba(${palette.secondary}, ${point.alpha * projected.alpha})`;
        context.fill();
    }

    function drawBursts(time) {
        if (!state.bursts.length) {
            return;
        }

        state.bursts = state.bursts.filter((burst) => {
            const age = time - burst.start;
            const lifespan = 900;

            if (age > lifespan) {
                return false;
            }

            const progress = age / lifespan;
            const radius = 14 + progress * (coarsePointer ? 48 : 74);
            const alpha = (1 - progress) * 0.55;

            context.beginPath();
            context.arc(burst.x, burst.y, radius, 0, Math.PI * 2);
            context.strokeStyle = `rgba(${palette.pink}, ${alpha})`;
            context.lineWidth = 2;
            context.stroke();

            context.beginPath();
            context.arc(burst.x, burst.y, radius * 0.45, 0, Math.PI * 2);
            context.fillStyle = `rgba(${palette.neon}, ${alpha * 0.33})`;
            context.fill();

            return true;
        });
    }

    function createFirework(x, y, startTime, intensity = 1) {
        const colorSets = [
            ['255, 79, 216', '255, 196, 70', '255, 255, 255'],
            ['57, 244, 255', '24, 130, 255', '255, 255, 255'],
            ['255, 116, 160', '255, 214, 132', '255, 255, 255'],
            ['132, 255, 201', '63, 166, 255', '255, 255, 255'],
        ];

        const paletteSet = colorSets[Math.floor(Math.random() * colorSets.length)];
        const particleCount = Math.floor((coarsePointer ? 24 : 44) * intensity);
        const gravity = randomBetween(0.22, 0.34);
        const lifespan = randomBetween(950, 1420);
        const particles = [];

        for (let i = 0; i < particleCount; i += 1) {
            const angle = (i / particleCount) * Math.PI * 2 + randomBetween(-0.11, 0.11);
            const speed = randomBetween(1.9, 4.9) * (coarsePointer ? 0.92 : 1);
            const chosenColor = paletteSet[Math.floor(Math.random() * paletteSet.length)];

            particles.push({
                x,
                y,
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed,
                drag: randomBetween(0.972, 0.985),
                size: randomBetween(1.2, 2.8),
                alpha: randomBetween(0.6, 1),
                color: chosenColor,
                trail: [],
            });
        }

        return {
            x,
            y,
            start: startTime,
            gravity,
            lifespan,
            particles,
            flashRadius: randomBetween(16, 28),
        };
    }

    function spawnFirework(x, y, timestamp, intensity = 1) {
        if (state.fireworks.length >= state.maxFireworks) {
            state.fireworks.shift();
        }

        state.fireworks.push(createFirework(x, y, timestamp, intensity));
    }

    function drawFireworks(time) {
        if (!state.fireworks.length) {
            return;
        }

        state.fireworks = state.fireworks.filter((firework) => {
            const age = time - firework.start;
            if (age > firework.lifespan) {
                return false;
            }

            const progress = age / firework.lifespan;
            const fade = Math.max(0, 1 - progress);

            if (progress < 0.16) {
                context.beginPath();
                context.arc(firework.x, firework.y, firework.flashRadius * (1 + progress * 1.8), 0, Math.PI * 2);
                context.fillStyle = `rgba(${palette.secondary}, ${0.24 * fade})`;
                context.fill();
            }

            for (const particle of firework.particles) {
                particle.vx *= particle.drag;
                particle.vy = particle.vy * particle.drag + firework.gravity * 0.085;

                particle.x += particle.vx;
                particle.y += particle.vy;

                particle.trail.push({ x: particle.x, y: particle.y });
                if (particle.trail.length > 5) {
                    particle.trail.shift();
                }

                if (particle.trail.length > 1) {
                    context.beginPath();
                    for (let i = 0; i < particle.trail.length; i += 1) {
                        const t = particle.trail[i];
                        if (i === 0) {
                            context.moveTo(t.x, t.y);
                        } else {
                            context.lineTo(t.x, t.y);
                        }
                    }

                    context.strokeStyle = `rgba(${particle.color}, ${fade * 0.18})`;
                    context.lineWidth = 0.8;
                    context.stroke();
                }

                context.beginPath();
                context.arc(particle.x, particle.y, particle.size * (0.35 + fade), 0, Math.PI * 2);
                context.fillStyle = `rgba(${particle.color}, ${particle.alpha * fade})`;
                context.fill();
            }

            return true;
        });
    }

    function maybeSpawnAutoFirework(timestamp) {
        if (timestamp - state.lastAutoFireworkAt < state.nextAutoFireworkDelay) {
            return;
        }

        const marginX = state.width * 0.14;
        const minX = marginX;
        const maxX = state.width - marginX;
        const minY = state.height * 0.12;
        const maxY = state.height * 0.58;

        spawnFirework(
            randomBetween(minX, maxX),
            randomBetween(minY, maxY),
            timestamp,
            randomBetween(0.88, 1.12),
        );

        state.lastAutoFireworkAt = timestamp;
        state.nextAutoFireworkDelay = randomBetween(850, coarsePointer ? 1850 : 1450);
    }

    function addBurst(clientX, clientY, timestamp) {
        const rect = hero.getBoundingClientRect();
        const x = clamp(clientX - rect.left, 0, rect.width);
        const y = clamp(clientY - rect.top, 0, rect.height);

        if (state.bursts.length >= state.maxBursts) {
            state.bursts.shift();
        }

        state.bursts.push({ x, y, start: timestamp });
    }

    function render(timestamp) {
        if (!state.running) {
            return;
        }

        if (!state.inView || document.hidden) {
            state.frameId = window.requestAnimationFrame(render);
            return;
        }

        context.clearRect(0, 0, state.width, state.height);

        const bgGradient = context.createRadialGradient(
            state.width * 0.5,
            state.height * 0.45,
            state.radius * 0.25,
            state.width * 0.5,
            state.height * 0.5,
            state.radius * 2.2,
        );
        bgGradient.addColorStop(0, 'rgba(255, 255, 255, 0.20)');
        bgGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

        context.fillStyle = bgGradient;
        context.fillRect(0, 0, state.width, state.height);

        maybeSpawnAutoFirework(timestamp);

        state.pointerEnergy += (state.targetEnergy - state.pointerEnergy) * 0.08;
        state.targetEnergy *= 0.94;

        const pointerInfluenceX = state.pointerX * 0.22;
        const pointerInfluenceY = state.pointerY * 0.12;

        state.rotationY += 0.0026 + pointerInfluenceX + state.pointerEnergy * 0.016;
        state.rotationX = clamp(-0.33 + pointerInfluenceY, -0.5, -0.12);

        for (const ring of state.rings) {
            drawRing(ring, timestamp);
        }

        const pointsByDepth = state.points
            .map((point) => ({ point, projected: projectAnimatedPoint(point, timestamp) }))
            .sort((a, b) => a.projected.z - b.projected.z);

        drawConnections(pointsByDepth);

        for (const projectedPoint of pointsByDepth) {
            drawPoint(projectedPoint.point, projectedPoint.projected);
        }

        drawBursts(timestamp);
        drawFireworks(timestamp);

        state.frameId = window.requestAnimationFrame(render);
    }

    function onPointerMove(event) {
        const rect = hero.getBoundingClientRect();
        const x = (event.clientX - rect.left) / Math.max(rect.width, 1);
        const y = (event.clientY - rect.top) / Math.max(rect.height, 1);

        state.pointerX = clamp(x - 0.5, -0.5, 0.5);
        state.pointerY = clamp(y - 0.5, -0.5, 0.5);

        hero.style.setProperty('--pointer-x', `${(x * 100).toFixed(2)}%`);
        hero.style.setProperty('--pointer-y', `${(y * 100).toFixed(2)}%`);

        if (state.lastPointerX !== null && state.lastPointerY !== null) {
            const dx = x - state.lastPointerX;
            const dy = y - state.lastPointerY;
            const velocity = Math.sqrt(dx * dx + dy * dy);
            state.targetEnergy = clamp(velocity * 9 + 0.03, 0, coarsePointer ? 0.18 : 0.35);
        }

        state.lastPointerX = x;
        state.lastPointerY = y;
    }

    function onPointerDown(event) {
        addBurst(event.clientX, event.clientY, performance.now());
        const rect = hero.getBoundingClientRect();
        const x = clamp(event.clientX - rect.left, 0, rect.width);
        const y = clamp(event.clientY - rect.top, 0, rect.height);
        spawnFirework(x, y, performance.now(), 1.08);
        state.targetEnergy = clamp(state.targetEnergy + 0.18, 0, coarsePointer ? 0.2 : 0.42);
    }

    function onPointerLeave() {
        state.pointerX = 0;
        state.pointerY = 0;
        state.targetEnergy = 0;
        state.lastPointerX = null;
        state.lastPointerY = null;
        hero.style.setProperty('--pointer-x', '50%');
        hero.style.setProperty('--pointer-y', '52%');
    }

    function onVisibilityChange() {
        if (!document.hidden && !state.frameId) {
            state.frameId = window.requestAnimationFrame(render);
        }
    }

    state.observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                state.inView = entry.isIntersecting && entry.intersectionRatio > 0.08;
            }
        },
        { threshold: [0, 0.08, 0.2] },
    );

    state.observer.observe(hero);

    resize();
    window.addEventListener('resize', resize, { passive: true });
    hero.addEventListener('pointermove', onPointerMove, { passive: true });
    hero.addEventListener('pointerdown', onPointerDown, { passive: true });
    hero.addEventListener('pointerleave', onPointerLeave, { passive: true });
    document.addEventListener('visibilitychange', onVisibilityChange, { passive: true });

    state.frameId = window.requestAnimationFrame(render);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHero3D, { once: true });
} else {
    initHero3D();
}
