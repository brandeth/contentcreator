<script setup lang="ts">
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue';

const HEADER_OFFSET = 64;

type StoryPanel = {
    eyebrow: string;
    title: string;
    summary: string;
    points: string[];
    imageUrl: string;
    imagePosition: string;
    accentClass: string;
    surfaceClass: string;
};

const panels: StoryPanel[] = [
    {
        eyebrow: 'Creator flow',
        title: 'Start with the asset you need.',
        summary:
            'PromptEdit keeps model choice, credits, and output history in one creator workspace so the first useful draft arrives faster.',
        points: [
            'Choose image, video, audio, or template tools',
            'Run variations against one credit wallet',
            'Save strong results back to the project',
        ],
        imageUrl:
            'https://plus.unsplash.com/premium_photo-1726869694722-66df5f9621b8?q=80&w=1079&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        imagePosition: 'object-center',
        accentClass: 'bg-brand-yellow-500',
        surfaceClass: 'bg-brand-neutral-0 text-brand-neutral-900',
    },
    {
        eyebrow: 'Generate',
        title: 'Scroll through a fuller production queue.',
        summary:
            'Longer panels can hold dense production detail while the slide stays pinned. The content moves first, then the next story panel takes over.',
        points: [
            'Thumbnail variations for launches and recaps',
            'Short-form hooks rendered for social cuts',
            'Voiceover passes grouped by project',
            'Music beds, loops, and impacts beside edits',
            'Brand kit exports kept with the source prompt',
            'Commercial-ready templates for finishing work',
            'Reusable prompts saved for the next campaign',
        ],
        imageUrl: 'https://www.pexels.com/download/video/6976218/',
        imagePosition: 'object-center',
        accentClass: 'bg-brand-pink-400',
        surfaceClass: 'bg-brand-pink-400 text-brand-neutral-900',
    },
    {
        eyebrow: 'Organize',
        title: 'Keep generated work with reusable assets.',
        summary:
            'Creators can move from raw output to a launch-ready set without hunting through separate subscriptions and download folders.',
        points: [
            'Group outputs by campaign',
            'Attach templates, sounds, and LUTs',
            'Return to the winning prompt any time',
        ],
        imageUrl:
            'https://images.unsplash.com/photo-1641391503184-a2131018701b?q=80&w=880&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        imagePosition: 'object-center',
        accentClass: 'bg-brand-teal-400',
        surfaceClass: 'bg-brand-neutral-900 text-white',
    },
    {
        eyebrow: 'Publish',
        title: 'Ship the finished edit faster.',
        summary:
            'The final handoff is a tidy asset stack: visuals, motion, audio, and reusable prompts ready for the next creator sprint.',
        points: [
            'Download usable files',
            'Share project-ready collections',
            'Use credits only when new generation is needed',
        ],
        imageUrl:
            'https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        imagePosition: 'object-center',
        accentClass: 'bg-brand-blue-400',
        surfaceClass: 'bg-brand-blue-400 text-brand-neutral-900',
    },
];

const wrapper = ref<HTMLElement | null>(null);
let cleanupAnimations: (() => void) | null = null;
let isComponentMounted = false;

const teardownAnimations = () => {
    cleanupAnimations?.();
    cleanupAnimations = null;
};

const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const canPinPanels = () => window.matchMedia('(min-width: 768px)').matches;

const initializePinnedPanels = async () => {
    const root = wrapper.value;

    if (!root || prefersReducedMotion() || !canPinPanels()) {
        root?.classList.add('is-static');
        return;
    }

    root.classList.remove('is-static');

    const [{ gsap }, { ScrollTrigger }] = await Promise.all([
        import('gsap'),
        import('gsap/ScrollTrigger'),
    ]);

    gsap.registerPlugin(ScrollTrigger);

    await nextTick();

    if (!isComponentMounted || !wrapper.value) {
        return;
    }

    const panelsToPin = gsap.utils.toArray<HTMLElement>(
        root.querySelectorAll('.pinned-story-panel'),
    );
    const activeTriggers: Array<{ kill: () => void }> = [];
    const activeTimelines: Array<{ kill: () => void }> = [];

    panelsToPin.slice(0, -1).forEach((panel) => {
        const frame = panel.querySelector<HTMLElement>(
            '.pinned-story-panel__frame',
        );
        const inner = panel.querySelector<HTMLElement>(
            '.pinned-story-panel__inner',
        );

        if (!frame || !inner) {
            return;
        }

        const getFrameHeight = () => frame.clientHeight;
        const getScrollDistance = () =>
            Math.max(inner.scrollHeight - getFrameHeight(), 0);
        const getPinDistance = () =>
            getScrollDistance() + panel.getBoundingClientRect().height;

        const timeline = gsap.timeline({
            scrollTrigger: {
                trigger: panel,
                start: `bottom bottom-=${HEADER_OFFSET}`,
                end: () => `+=${getPinDistance()}`,
                invalidateOnRefresh: true,
                pin: true,
                pinSpacing: false,
                scrub: true,
                onRefresh: () => {
                    const scrollDistance = getScrollDistance();

                    panel.style.marginBottom = scrollDistance
                        ? `${scrollDistance}px`
                        : '';
                },
            },
        });

        timeline.to(inner, {
            y: () => -getScrollDistance(),
            duration: () => Math.max(getScrollDistance(), 0.001),
            ease: 'none',
        });

        timeline
            .fromTo(
                panel,
                { opacity: 1, scale: 1 },
                {
                    opacity: 0.5,
                    scale: 0.86,
                    duration: () => panel.getBoundingClientRect().height * 0.9,
                    ease: 'none',
                },
            )
            .to(panel, {
                opacity: 0,
                duration: () => panel.getBoundingClientRect().height * 0.1,
                ease: 'none',
            });

        activeTimelines.push(timeline);

        if (timeline.scrollTrigger) {
            activeTriggers.push(timeline.scrollTrigger);
        }
    });

    ScrollTrigger.refresh();

    cleanupAnimations = () => {
        activeTimelines.forEach((timeline) => timeline.kill());
        activeTriggers.forEach((trigger) => trigger.kill());
        panelsToPin.forEach((panel) => {
            panel.style.marginBottom = '';
            gsap.set(panel, { clearProps: 'all' });
            gsap.set(panel.querySelector('.pinned-story-panel__inner'), {
                clearProps: 'transform',
            });
        });
    };
};

onMounted(() => {
    isComponentMounted = true;
    initializePinnedPanels();
});

onBeforeUnmount(() => {
    isComponentMounted = false;
    teardownAnimations();
});
</script>

<template>
    <section
        ref="wrapper"
        class="pinned-story-panels bg-brand-neutral-100 text-brand-neutral-900"
        aria-label="Creator marketplace workflow"
    >
        <article
            v-for="panel in panels"
            :key="panel.title"
            :class="[
                'pinned-story-panel relative flex min-h-[calc(100svh-4rem)] items-stretch overflow-visible border-y border-brand-neutral-900/10 md:overflow-hidden',
                panel.surfaceClass,
            ]"
        >
            <div
                class="pinned-story-panel__frame flex w-full items-stretch overflow-visible md:overflow-hidden"
            >
                <div
                    class="pinned-story-panel__inner mx-auto grid min-h-full w-full max-w-7xl gap-8 px-5 py-10 sm:px-8 md:grid-cols-[0.9fr_1.1fr] md:items-center lg:gap-12 lg:py-14"
                >
                    <div class="flex flex-col items-start">
                        <span
                            :class="[
                                'rounded-full border border-brand-neutral-900 px-3 py-1 text-xs font-extrabold text-brand-neutral-900 uppercase shadow-[2px_2px_0_0_var(--color-brand-neutral-900)]',
                                panel.accentClass,
                            ]"
                        >
                            {{ panel.eyebrow }}
                        </span>

                        <h2
                            class="mt-6 max-w-2xl text-4xl leading-tight font-extrabold sm:text-5xl lg:text-6xl"
                        >
                            {{ panel.title }}
                        </h2>

                        <p
                            class="mt-5 max-w-xl text-base leading-7 font-medium opacity-80 sm:text-lg"
                        >
                            {{ panel.summary }}
                        </p>
                    </div>

                    <div
                        class="grid gap-5 lg:grid-cols-[1fr_0.85fr] lg:items-center"
                    >
                        <div
                            class="relative aspect-4/5 min-h-0 overflow-hidden rounded-lg border border-brand-neutral-900 bg-brand-neutral-0 shadow-[4px_4px_0_0_var(--color-brand-neutral-900)]"
                        >
                            <video
                                v-if="
                                    panel.imageUrl.includes(
                                        'pexels.com/download/video',
                                    )
                                "
                                :src="panel.imageUrl"
                                class="h-full w-full object-cover"
                                autoplay
                                muted
                                loop
                                playsinline
                            />
                            <img
                                v-else
                                :src="panel.imageUrl"
                                :alt="panel.title"
                                :class="[
                                    'h-full w-full object-cover',
                                    panel.imagePosition,
                                ]"
                            />
                            <div
                                class="absolute inset-x-4 bottom-4 flex items-center justify-between gap-3 rounded-md border border-brand-neutral-900 bg-brand-neutral-0 p-3 text-brand-neutral-900"
                            >
                                <span class="text-xs font-extrabold uppercase">
                                    PromptEdit
                                </span>
                                <span
                                    :class="[
                                        'h-3 w-24 rounded-full border border-brand-neutral-900',
                                        panel.accentClass,
                                    ]"
                                />
                            </div>
                        </div>

                        <div class="grid gap-3">
                            <div
                                v-for="(point, index) in panel.points"
                                :key="point"
                                class="flex min-h-16 items-center gap-3 rounded-lg border border-brand-neutral-900 bg-brand-neutral-0 p-4 text-brand-neutral-900 shadow-[2px_2px_0_0_var(--color-brand-neutral-900)]"
                            >
                                <span
                                    :class="[
                                        'grid size-9 shrink-0 place-items-center rounded-full border border-brand-neutral-900 text-sm font-extrabold',
                                        panel.accentClass,
                                    ]"
                                >
                                    {{ index + 1 }}
                                </span>
                                <span class="text-sm leading-5 font-bold">
                                    {{ point }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </section>
</template>

<style scoped>
.pinned-story-panels {
    overscroll-behavior-y: contain;
}

.pinned-story-panel {
    transform-origin: center top;
    will-change: transform, opacity;
}

.pinned-story-panel__frame {
    max-height: none;
}

.pinned-story-panel__inner {
    will-change: transform;
}

.pinned-story-panels.is-static .pinned-story-panel {
    min-height: auto;
    transform: none;
    opacity: 1;
}

.pinned-story-panels.is-static .pinned-story-panel__frame {
    max-height: none;
    overflow: visible;
}

.pinned-story-panels.is-static .pinned-story-panel__inner {
    transform: none;
}

@media (min-width: 768px) {
    .pinned-story-panel__frame {
        max-height: calc(100svh - 4rem);
    }
}
</style>
