<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

type FlashcardAsset = {
    type: 'image' | 'video';
    url: string;
    fallbackUrl?: string;
};

type Particle = {
    x: number;
    y: number;
    scale: number;
    rotate: number;
    media: HTMLImageElement | HTMLVideoElement;
    fallback: HTMLImageElement;
};

const imageUrls = [
    'https://plus.unsplash.com/premium_photo-1664272436668-78437b92929e?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    'https://images.unsplash.com/flagged/photo-1572392640988-ba48d1a74457?q=80&w=764&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=745&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    'https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    'https://plus.unsplash.com/premium_photo-1695405363183-e55554168063?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    'https://images.unsplash.com/photo-1634986666676-ec8fd927c23d?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    'https://images.unsplash.com/photo-1641391503184-a2131018701b?q=80&w=880&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    'https://plus.unsplash.com/premium_photo-1726869694722-66df5f9621b8?q=80&w=1079&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    'https://plus.unsplash.com/premium_photo-1683140802177-372401d5c783?q=80&w=1198&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
];
const videoUrls = [
    'https://www.pexels.com/download/video/34013104/',
    'https://www.pexels.com/download/video/9153299/',
    'https://www.pexels.com/download/video/7598768/',
    'https://www.pexels.com/download/video/18069233/',
    'https://www.pexels.com/download/video/5405659/',
    'https://www.pexels.com/download/video/6976218/',
    'https://www.pexels.com/download/video/7778853/',
    'https://www.pexels.com/download/video/4540152/',
    'https://www.pexels.com/download/video/12336865/',
];
const PARTICLE_COUNT = 99;
const MAX_PIXEL_RATIO = 1.5;
const CARD_RADIUS = 24;

const flashcardAssets: FlashcardAsset[] = [
    ...imageUrls.map((url) => ({ type: 'image' as const, url })),
    ...videoUrls.map((url, index) => ({
        type: 'video' as const,
        url,
        fallbackUrl: imageUrls[index % imageUrls.length],
    })),
];

const canvas = ref<HTMLCanvasElement | null>(null);
const root = ref<HTMLElement | null>(null);
let cleanupAnimation: (() => void) | null = null;
let isComponentMounted = false;

const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const createImage = (url: string) => {
    const image = new Image();
    image.decoding = 'async';
    image.loading = 'lazy';
    image.src = url;

    return image;
};

const createVideo = (url: string) => {
    const video = document.createElement('video');
    video.src = url;
    video.muted = true;
    video.loop = true;
    video.playsInline = true;
    video.preload = 'metadata';

    return video;
};

const getMediaSize = (media: HTMLImageElement | HTMLVideoElement) => {
    if (media instanceof HTMLVideoElement) {
        return {
            width: media.videoWidth,
            height: media.videoHeight,
        };
    }

    return {
        width: media.naturalWidth,
        height: media.naturalHeight,
    };
};

const drawCoveredMedia = (
    context: CanvasRenderingContext2D,
    media: HTMLImageElement | HTMLVideoElement,
    x: number,
    y: number,
    width: number,
    height: number,
) => {
    const mediaSize = getMediaSize(media);

    if (!mediaSize.width || !mediaSize.height || width <= 0 || height <= 0) {
        return;
    }

    const mediaRatio = mediaSize.width / mediaSize.height;
    const frameRatio = width / height;
    const sourceWidth =
        mediaRatio > frameRatio
            ? mediaSize.height * frameRatio
            : mediaSize.width;
    const sourceHeight =
        mediaRatio > frameRatio
            ? mediaSize.height
            : mediaSize.width / frameRatio;
    const sourceX = (mediaSize.width - sourceWidth) / 2;
    const sourceY = (mediaSize.height - sourceHeight) / 2;

    context.drawImage(
        media,
        sourceX,
        sourceY,
        sourceWidth,
        sourceHeight,
        x,
        y,
        width,
        height,
    );
};

const createRoundedRectPath = (
    context: CanvasRenderingContext2D,
    x: number,
    y: number,
    width: number,
    height: number,
    radius: number,
) => {
    const cornerRadius = Math.min(radius, width / 2, height / 2);

    context.beginPath();
    context.moveTo(x + cornerRadius, y);
    context.lineTo(x + width - cornerRadius, y);
    context.quadraticCurveTo(x + width, y, x + width, y + cornerRadius);
    context.lineTo(x + width, y + height - cornerRadius);
    context.quadraticCurveTo(
        x + width,
        y + height,
        x + width - cornerRadius,
        y + height,
    );
    context.lineTo(x + cornerRadius, y + height);
    context.quadraticCurveTo(x, y + height, x, y + height - cornerRadius);
    context.lineTo(x, y + cornerRadius);
    context.quadraticCurveTo(x, y, x + cornerRadius, y);
    context.closePath();
};

const createAnimation = async () => {
    const section = root.value;
    const particleCanvas = canvas.value;

    if (!section || !particleCanvas || prefersReducedMotion()) {
        return;
    }

    const context = particleCanvas.getContext('2d', { alpha: true });

    if (!context) {
        return;
    }

    const [{ gsap }] = await Promise.all([import('gsap')]);

    if (!isComponentMounted || !root.value || !canvas.value) {
        return;
    }

    const sharedMedia = flashcardAssets.map((asset) => {
        if (asset.type === 'video') {
            return createVideo(asset.url);
        }

        return createImage(asset.url);
    });
    const fallbackImages = flashcardAssets.map((asset) =>
        createImage(asset.fallbackUrl ?? asset.url),
    );
    const particles = Array.from({ length: PARTICLE_COUNT }, (_, index) => {
        const assetIndex = index % sharedMedia.length;

        return {
            x: 0,
            y: 0,
            scale: 0,
            rotate: 0,
            media: sharedMedia[assetIndex],
            fallback: fallbackImages[assetIndex],
        } satisfies Particle;
    });
    let canvasWidth = 0;
    let canvasHeight = 0;
    let cardWidth = 0;
    let cardHeight = 0;
    let radius = 0;
    let pixelRatio = 1;
    let drawOrder = [...particles];
    let timeline: gsap.core.Timeline | null = null;

    const resizeCanvas = () => {
        const bounds = section.getBoundingClientRect();
        pixelRatio = Math.min(window.devicePixelRatio || 1, MAX_PIXEL_RATIO);
        canvasWidth = Math.max(1, Math.round(bounds.width));
        canvasHeight = Math.max(1, Math.round(bounds.height));
        particleCanvas.width = Math.round(canvasWidth * pixelRatio);
        particleCanvas.height = Math.round(canvasHeight * pixelRatio);
        context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        radius = Math.max(canvasWidth, canvasHeight) * 0.95;
        cardWidth = Math.min(260, Math.max(120, canvasWidth * 0.15));
        cardHeight = cardWidth * 1.28;
    };
    const getDrawableMedia = (particle: Particle) => {
        const fallback = particle.fallback;

        if (particle.media instanceof HTMLVideoElement) {
            return particle.media.readyState >=
                HTMLMediaElement.HAVE_CURRENT_DATA
                ? particle.media
                : fallback;
        }

        return particle.media.complete && particle.media.naturalWidth
            ? particle.media
            : fallback;
    };
    const draw = () => {
        context.clearRect(0, 0, canvasWidth, canvasHeight);
        drawOrder.forEach((particle) => {
            const renderedWidth = cardWidth * particle.scale;
            const renderedHeight = cardHeight * particle.scale;

            context.save();
            context.translate(canvasWidth / 2, canvasHeight / 2);
            context.rotate(particle.rotate);
            createRoundedRectPath(
                context,
                particle.x,
                particle.y,
                renderedWidth,
                renderedHeight,
                CARD_RADIUS * particle.scale,
            );
            context.clip();
            drawCoveredMedia(
                context,
                getDrawableMedia(particle),
                particle.x,
                particle.y,
                renderedWidth,
                renderedHeight,
            );
            context.restore();
        });
    };
    const createTimeline = () => {
        drawOrder = [...particles].sort((a, b) => a.scale - b.scale);
        timeline?.kill();
        timeline = gsap
            .timeline({ onUpdate: draw })
            .fromTo(
                particles,
                {
                    x: (index: number) => {
                        const angle =
                            (index / particles.length) * Math.PI * 2 -
                            Math.PI / 2;

                        return Math.cos(angle * 10) * radius;
                    },
                    y: (index: number) => {
                        const angle =
                            (index / particles.length) * Math.PI * 2 -
                            Math.PI / 2;

                        return Math.sin(angle * 10) * radius;
                    },
                    scale: 1.35,
                    rotate: 0,
                },
                {
                    duration: 6,
                    ease: 'sine.inOut',
                    x: 0,
                    y: 0,
                    scale: 0,
                    rotate: -3,
                    stagger: { each: -0.055, repeat: -1 },
                },
                0,
            )
            .seek(100);
        timeline.pause(0);
        draw();
    };
    const playVideos = () => {
        sharedMedia.forEach((media) => {
            if (media instanceof HTMLVideoElement) {
                void media.play().catch(() => undefined);
            }
        });
    };
    const pauseVideos = () => {
        sharedMedia.forEach((media) => {
            if (media instanceof HTMLVideoElement) {
                media.pause();
            }
        });
    };
    const startAnimation = () => {
        if (!timeline || !timeline.paused()) {
            return;
        }

        playVideos();
        timeline.timeScale(0.55).play();
    };
    const stopAnimation = () => {
        timeline?.pause();
        pauseVideos();
    };
    const handleResize = () => {
        resizeCanvas();
        createTimeline();
        draw();
    };
    const handlePointerUp = () => {
        if (timeline?.paused()) {
            startAnimation();

            return;
        }

        stopAnimation();
    };
    const handleVisibilityChange = () => {
        if (document.hidden) {
            stopAnimation();

            return;
        }

        startAnimation();
    };

    resizeCanvas();
    createTimeline();
    window.addEventListener('resize', handleResize);
    document.addEventListener('visibilitychange', handleVisibilityChange);
    particleCanvas.addEventListener('pointerup', handlePointerUp);
    sharedMedia.forEach((media) => {
        media.addEventListener('loadeddata', draw, { once: true });
        media.addEventListener('load', draw, { once: true });
    });
    fallbackImages.forEach((image) => {
        image.addEventListener('load', draw, { once: true });
    });
    draw();
    startAnimation();

    cleanupAnimation = () => {
        window.removeEventListener('resize', handleResize);
        document.removeEventListener(
            'visibilitychange',
            handleVisibilityChange,
        );
        particleCanvas.removeEventListener('pointerup', handlePointerUp);
        timeline?.kill();
        pauseVideos();
        context.clearRect(0, 0, canvasWidth, canvasHeight);
    };
};

onMounted(() => {
    isComponentMounted = true;
    void createAnimation();
});

onBeforeUnmount(() => {
    isComponentMounted = false;
    cleanupAnimation?.();
    cleanupAnimation = null;
});
</script>

<template>
    <section
        ref="root"
        class="h-svh min-h-svh overflow-hidden bg-brand-neutral-900"
    >
        <canvas ref="canvas" class="block h-full min-h-svh w-full" />
    </section>
</template>
