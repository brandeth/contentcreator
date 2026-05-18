<script setup lang="ts">
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue';

type WorkflowPoint = {
    title: string;
    text: string;
    mediaUrl: string;
    mediaType: 'image' | 'video';
    positionClass: string;
    tiltClass: string;
    labelPositionClass: string;
    labelClass: string;
};

const workflowPoints: WorkflowPoint[] = [
    {
        title: 'Prompt',
        text: 'Choose a model and describe the asset you need.',
        mediaUrl:
            'https://plus.unsplash.com/premium_photo-1726869694722-66df5f9621b8?q=80&w=1079&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        mediaType: 'image',
        positionClass:
            'creator-workflow-motion__initial left-[55%] top-[5%] sm:left-[60%]',
        tiltClass: 'tilt-right',
        labelPositionClass: 'top-full mt-3',
        labelClass: 'right-0 left-auto translate-x-0',
    },
    {
        title: 'Generate',
        text: 'Run image, video, and audio variations from one credit wallet.',
        mediaUrl: 'https://www.pexels.com/download/video/6976218/',
        mediaType: 'video',
        positionClass: 'left-[6%] top-[22%] sm:left-[10%]',
        tiltClass: 'tilt-left',
        labelPositionClass: 'top-full mt-3',
        labelClass: 'left-0 translate-x-0',
    },
    {
        title: 'Render',
        text: 'Track the queue as drafts, clips, voiceovers, and packs finish.',
        mediaUrl:
            'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=745&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        mediaType: 'image',
        positionClass: 'right-[6%] top-[39%] sm:right-[10%]',
        tiltClass: 'tilt-right',
        labelPositionClass: 'top-full mt-3',
        labelClass: 'right-0 left-auto translate-x-0',
    },
    {
        title: 'Organize',
        text: 'Save results beside templates, sounds, LUTs, and edits.',
        mediaUrl:
            'https://images.unsplash.com/photo-1641391503184-a2131018701b?q=80&w=880&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        mediaType: 'image',
        positionClass: 'left-[10%] top-[57%] sm:left-[20%]',
        tiltClass: 'tilt-left',
        labelPositionClass: 'top-full mt-3',
        labelClass: 'left-0 translate-x-0 sm:left-1/2 sm:-translate-x-1/2',
    },
    {
        title: 'Package',
        text: 'Bundle the best outputs into a campaign-ready asset set.',
        mediaUrl: 'https://www.pexels.com/download/video/12336865/',
        mediaType: 'video',
        positionClass: 'left-[50%] top-[76%] sm:left-[60%]',
        tiltClass: 'tilt-right',
        labelPositionClass: 'bottom-full mb-3',
        labelClass: 'right-0 left-auto translate-x-0',
    },
    {
        title: 'Publish',
        text: 'Drop finished assets into your editor and ship faster.',
        mediaUrl:
            'https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        mediaType: 'image',
        positionClass: 'left-[6%] top-[90%] sm:left-[15%]',
        tiltClass: 'tilt-left',
        labelPositionClass: 'bottom-full mb-3',
        labelClass: 'left-0 translate-x-0',
    },
];

const root = ref<HTMLElement | null>(null);
let animationContext: { revert: () => void } | null = null;
let resizeCleanup: (() => void) | null = null;
let isComponentMounted = false;

const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const teardownAnimation = () => {
    animationContext?.revert();
    animationContext = null;
};

const createGuidePath = (points: Array<{ x: number; y: number }>) => {
    if (points.length < 2) {
        return '';
    }

    const curviness = 1.5;
    const controlScale = curviness / 6;

    return points.slice(0, -1).reduce((path, point, index) => {
        const previousPoint = points[Math.max(0, index - 1)];
        const nextPoint = points[index + 1];
        const nextNextPoint = points[Math.min(points.length - 1, index + 2)];
        const controlPointOne = {
            x: point.x + (nextPoint.x - previousPoint.x) * controlScale,
            y: point.y + (nextPoint.y - previousPoint.y) * controlScale,
        };
        const controlPointTwo = {
            x: nextPoint.x - (nextNextPoint.x - point.x) * controlScale,
            y: nextPoint.y - (nextNextPoint.y - point.y) * controlScale,
        };

        if (index === 0) {
            return `M ${point.x} ${point.y} C ${controlPointOne.x} ${controlPointOne.y}, ${controlPointTwo.x} ${controlPointTwo.y}, ${nextPoint.x} ${nextPoint.y}`;
        }

        return `${path} C ${controlPointOne.x} ${controlPointOne.y}, ${controlPointTwo.x} ${controlPointTwo.y}, ${nextPoint.x} ${nextPoint.y}`;
    }, '');
};

const createMotionPath = async () => {
    const section = root.value;

    teardownAnimation();

    if (!section || prefersReducedMotion()) {
        section?.classList.add('is-static');
        return;
    }

    section.classList.remove('is-static');

    const [{ gsap }, { ScrollTrigger }, { MotionPathPlugin }] =
        await Promise.all([
            import('gsap'),
            import('gsap/ScrollTrigger'),
            import('gsap/MotionPathPlugin'),
        ]);

    gsap.registerPlugin(ScrollTrigger, MotionPathPlugin);

    await nextTick();

    if (!isComponentMounted || !root.value) {
        return;
    }

    animationContext = gsap.context(() => {
        const asset = section.querySelector<HTMLElement>(
            '.creator-workflow-motion__asset',
        );
        const stage = section.querySelector<HTMLElement>(
            '.creator-workflow-motion__stage',
        );
        const pathGuide = section.querySelector<SVGPathElement>(
            '.creator-workflow-motion__path-guide-path',
        );
        const initialMarker = section.querySelector<HTMLElement>(
            '.creator-workflow-motion__initial',
        );
        const waypointPoints = gsap.utils.toArray<HTMLElement>(
            section.querySelectorAll('.creator-workflow-motion__point'),
        );
        const targetMarkers = gsap.utils.toArray<HTMLElement>(
            section.querySelectorAll(
                '.creator-workflow-motion__point:not(.creator-workflow-motion__initial) .creator-workflow-motion__marker',
            ),
        );
        const finalMarker = section.querySelector<HTMLElement>(
            '.creator-workflow-motion__final',
        );

        if (
            !asset ||
            !stage ||
            !pathGuide ||
            !initialMarker ||
            !waypointPoints.length ||
            !targetMarkers.length ||
            !finalMarker
        ) {
            return;
        }

        const assetStartRect = asset.getBoundingClientRect();
        const stageRect = stage.getBoundingClientRect();
        const assetStartX = assetStartRect.left + assetStartRect.width / 2;
        const assetStartY = assetStartRect.top + assetStartRect.height / 2;
        const path = targetMarkers.map((marker) => {
            const rect = marker.getBoundingClientRect();

            return {
                x: rect.left + rect.width / 2 - assetStartX,
                y: rect.top + rect.height / 2 - assetStartY,
            };
        });
        const guidePoints = [
            {
                x: assetStartX - stageRect.left,
                y: assetStartY - stageRect.top,
            },
            ...targetMarkers.map((marker) => {
                const rect = marker.getBoundingClientRect();

                return {
                    x: rect.left + rect.width / 2 - stageRect.left,
                    y: rect.top + rect.height / 2 - stageRect.top,
                };
            }),
        ];

        pathGuide.setAttribute('d', createGuidePath(guidePoints));
        const getInitialMarkerOffset = () =>
            initialMarker.getBoundingClientRect().top -
            section.getBoundingClientRect().top;
        const clearWaypointCards = () => {
            waypointPoints.forEach((point) => {
                point.classList.remove('is-active', 'is-reached');
            });
        };
        const updateWaypointCards = (
            progress: number,
            hasReachedStart = true,
        ) => {
            if (!hasReachedStart) {
                clearWaypointCards();

                return;
            }

            const waypointProgress = progress * (waypointPoints.length - 1);
            const activeIndex = Math.min(
                waypointPoints.length - 1,
                Math.max(0, Math.round(waypointProgress)),
            );

            waypointPoints.forEach((point, index) => {
                point.classList.toggle('is-active', index === activeIndex);
                point.classList.toggle(
                    'is-reached',
                    index <= waypointProgress + 0.08,
                );
            });
        };

        gsap.set(asset, { x: 0, y: 0 });
        clearWaypointCards();

        gsap.timeline({
            scrollTrigger: {
                trigger: section,
                start: () => `clamp(top+=${getInitialMarkerOffset()} center)`,
                endTrigger: finalMarker,
                end: 'clamp(top center)',
                scrub: 1,
                invalidateOnRefresh: true,
                onEnter: () => updateWaypointCards(0),
                onEnterBack: (self: { progress: number }) =>
                    updateWaypointCards(self.progress),
                onLeaveBack: clearWaypointCards,
                onUpdate: (self: { progress: number; isActive: boolean }) =>
                    updateWaypointCards(
                        self.progress,
                        self.isActive || self.progress > 0,
                    ),
            },
        }).to(asset, {
            duration: 1,
            ease: 'none',
            motionPath: {
                path,
                curviness: 1.5,
            },
        });
    }, section);

    ScrollTrigger.refresh();
};

onMounted(() => {
    isComponentMounted = true;
    createMotionPath();

    const handleResize = () => {
        createMotionPath();
    };

    window.addEventListener('resize', handleResize);
    resizeCleanup = () => window.removeEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
    isComponentMounted = false;
    resizeCleanup?.();
    resizeCleanup = null;
    teardownAnimation();
});
</script>

<template>
    <section
        ref="root"
        class="creator-workflow-motion bg-brand-neutral-900 pb-32 text-white sm:pb-44"
        aria-label="Creator workflow"
    >
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-5 pt-20 sm:px-8">
            <div>
                <p class="text-sm font-bold text-brand-yellow-500 uppercase">
                    Creator workflow
                </p>
                <h2
                    class="mt-3 max-w-4xl text-4xl leading-tight font-extrabold sm:text-5xl"
                >
                    From prompt to publish-ready asset without losing momentum.
                </h2>
            </div>
            <p class="max-w-4xl text-base leading-7 text-white/65">
                Follow one generated asset as it moves through the production
                path: prompt, generate, render, organize, package, and publish.
            </p>
        </div>

        <div
            class="creator-workflow-motion__stage relative mx-auto h-[560svh] min-h-1280 max-w-7xl px-5 pb-40 sm:px-8"
        >
            <div
                class="pointer-events-none absolute inset-x-5 top-8 bottom-8 opacity-55 sm:inset-x-8"
                aria-hidden="true"
            >
                <div class="creator-workflow-motion__grid h-full w-full" />
            </div>

            <svg
                class="creator-workflow-motion__path-guide pointer-events-none absolute inset-0 z-1 size-full"
                aria-hidden="true"
            >
                <path class="creator-workflow-motion__path-guide-path" />
            </svg>

            <div
                v-for="(point, index) in workflowPoints"
                :key="point.title"
                :class="[
                    'creator-workflow-motion__point absolute z-10 flex items-center justify-center',
                    point.positionClass,
                    point.tiltClass,
                ]"
            >
                <div
                    class="creator-workflow-motion__marker grid place-items-center border-2 border-dashed border-brand-neutral-0/35 bg-brand-neutral-900/80 backdrop-blur"
                >
                    <figure
                        class="creator-workflow-motion__media-card overflow-hidden border border-brand-neutral-0/60 bg-brand-neutral-100 shadow-[5px_5px_0_0_var(--color-brand-neutral-0)]"
                        aria-hidden="true"
                    >
                        <img
                            v-if="point.mediaType === 'image'"
                            :src="point.mediaUrl"
                            alt=""
                            class="size-full object-cover"
                        />
                        <video
                            v-else
                            :src="point.mediaUrl"
                            class="size-full object-cover"
                            muted
                            playsinline
                            loop
                            autoplay
                        />
                    </figure>

                    <div
                        v-if="index === 0"
                        class="creator-workflow-motion__asset absolute z-20 size-25 bg-contain bg-center bg-no-repeat"
                        aria-hidden="true"
                    />
                </div>

                <div
                    :class="[
                        'creator-workflow-motion__label pointer-events-none absolute w-[min(16rem,72vw)] border border-brand-neutral-0/30 bg-brand-neutral-900/88 p-3 shadow-[3px_3px_0_0_var(--color-brand-neutral-0)] backdrop-blur',
                        point.labelPositionClass,
                        point.labelClass,
                    ]"
                >
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-base leading-tight font-extrabold">
                            {{ point.title }}
                        </h3>
                        <span class="text-xs font-extrabold text-white/40">
                            0{{ index + 1 }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm leading-5 text-white/65">
                        {{ point.text }}
                    </p>
                </div>
            </div>

            <div
                class="creator-workflow-motion__final absolute bottom-0 left-0 h-px w-px"
            />
        </div>
    </section>
</template>

<style scoped>
.creator-workflow-motion {
    overflow-x: clip;
    overscroll-behavior-y: auto;
}

.creator-workflow-motion__grid {
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.07) 2px, transparent 2px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.07) 2px, transparent 2px),
        linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
    background-position:
        -2px -2px,
        -2px -2px,
        -1px -1px,
        -1px -1px;
    background-size:
        100px 100px,
        100px 100px,
        20px 20px,
        20px 20px;
}

.creator-workflow-motion__path-guide-path {
    fill: none;
    stroke: var(--color-brand-yellow-500);
    stroke-dasharray: 12 14;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 3;
    opacity: 0.62;
}

.creator-workflow-motion__marker,
.creator-workflow-motion__point,
.creator-workflow-motion__label,
.creator-workflow-motion__media-card,
.creator-workflow-motion__asset {
    border-radius: var(--radius);
}

.creator-workflow-motion__point {
    width: clamp(16rem, calc(19rem + 3vw), 25rem);
    aspect-ratio: 5 / 7;
}

.creator-workflow-motion__marker {
    width: 100%;
    height: 100%;
    padding: clamp(0.375rem, 0.8vw, 0.625rem);
}

.creator-workflow-motion__media-card {
    width: 100%;
    aspect-ratio: 5 / 7;
    transform-origin: 50% 60%;
    opacity: 0.78;
    filter: saturate(0.85) contrast(0.95);
    transition:
        transform 420ms ease,
        opacity 420ms ease,
        filter 420ms ease;
}

.creator-workflow-motion__point.is-reached.tilt-left
    .creator-workflow-motion__media-card {
    transform: rotate(-8deg) scale(0.98);
}

.creator-workflow-motion__point.is-reached.tilt-right
    .creator-workflow-motion__media-card {
    transform: rotate(8deg) scale(0.98);
}

.creator-workflow-motion__point.is-reached
    .creator-workflow-motion__media-card {
    opacity: 0.94;
    filter: saturate(1.08) contrast(1.04);
}

.creator-workflow-motion__point.is-active.tilt-left
    .creator-workflow-motion__media-card {
    transform: rotate(-8deg) scale(1.02);
}

.creator-workflow-motion__point.is-active.tilt-right
    .creator-workflow-motion__media-card {
    transform: rotate(8deg) scale(1.02);
}

.creator-workflow-motion__point.is-active .creator-workflow-motion__media-card {
    opacity: 1;
    filter: saturate(1.18) contrast(1.08);
}

.creator-workflow-motion__asset {
    top: calc(50% - 50px);
    left: calc(50% - 50px);
    background-image: url('https://assets.codepen.io/16327/flair-26.png');
    will-change: transform;
}

.creator-workflow-motion.is-static .creator-workflow-motion__stage {
    height: auto;
    padding-block: 3rem;
}

.creator-workflow-motion.is-static .creator-workflow-motion__point {
    position: relative;
    inset: auto;
    margin-block: 1rem;
    margin-inline: auto;
    padding-inline: 0;
}

.creator-workflow-motion.is-static .creator-workflow-motion__asset {
    display: none;
}
</style>
