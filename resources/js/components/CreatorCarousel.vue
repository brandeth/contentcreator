<script setup lang="ts">
import { ChevronLeft, ChevronRight, Music2, Play } from 'lucide-vue-next';
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

type Creator = {
    name: string;
    category: string;
    metric: string;
    usage: string;
    imageUrl: string;
    accent: string;
    position: string;
    details?: string;
};

const props = defineProps<{
    creators: Creator[];
}>();

const visibleCreators = computed(() => props.creators.slice(0, 5));
const creatorCards = ref<Creator[]>([]);
const selectedCreator = ref<Creator | null>(null);
const isDetailOpen = ref(false);
const isDetailFlipped = ref(false);

const frontCreator = computed(
    () => creatorCards.value[creatorCards.value.length - 1] ?? null,
);

const carouselSection = ref<HTMLElement | null>(null);
const creatorSlider = ref<HTMLElement | null>(null);
const mobileGallery = ref<HTMLElement | null>(null);
const mobileDragProxy = ref<HTMLElement | null>(null);
let activeFlip: { kill: () => void } | null = null;
let mobileGsapContext: { revert: () => void } | null = null;
let mobileDraggable: Array<{ kill: () => void }> = [];
let mobileMediaQuery: MediaQueryList | null = null;
let revealObserver: IntersectionObserver | null = null;
let revealTransitionTimer: number | null = null;
let revealFallbackFrame: number | null = null;
let revealFallbackTimer: number | null = null;
let isAnimating = false;
const isDesktopDeckRevealed = ref(false);
const isDesktopDeckRevealSettled = ref(false);
const isMobileDeckRevealed = ref(false);
const mobileActiveIndex = ref(0);

watch(
    visibleCreators,
    (creators) => {
        creatorCards.value = [...creators];
        mobileActiveIndex.value = Math.min(
            mobileActiveIndex.value,
            Math.max(creators.length - 1, 0),
        );
    },
    { immediate: true },
);

const getWrappedOffset = (
    index: number,
    activeIndex: number,
    total: number,
) => {
    const midpoint = Math.floor(total / 2);

    return ((index - activeIndex + total + midpoint) % total) - midpoint;
};

const renderMobileCarousel = async (animate = true, duration = 0.55) => {
    const gallery = mobileGallery.value;
    const total = visibleCreators.value.length;

    if (!gallery || total === 0) {
        return;
    }

    const { gsap } = await import('gsap');
    const cards = gsap.utils.toArray<HTMLElement>(
        gallery.querySelectorAll('.mobile-creator-card'),
    );
    const galleryWidth = gallery.getBoundingClientRect().width;
    const spreadDistance = Math.min(
        galleryWidth * (galleryWidth < 640 ? 0.56 : 0.34),
        galleryWidth < 640 ? 230 : 290,
    );

    cards.forEach((card, index) => {
        const offset = getWrappedOffset(index, mobileActiveIndex.value, total);
        const distance = Math.abs(offset);
        const isRevealed = isMobileDeckRevealed.value;
        const state = {
            xPercent: -50,
            x: isRevealed ? offset * spreadDistance : offset * 8,
            y: isRevealed ? distance * 18 : distance * 8,
            rotate: isRevealed ? offset * 4.5 : offset * 1.5,
            scale: isRevealed
                ? distance === 0
                    ? 1
                    : distance === 1
                      ? 0.84
                      : 0.7
                : distance === 0
                  ? 1
                  : 0.92 - distance * 0.04,
            opacity: isRevealed
                ? distance > 2
                    ? 0
                    : distance === 2
                      ? 0.48
                      : 1
                : distance > 2
                  ? 0
                  : 1,
            zIndex: 20 - distance,
        };

        if (animate) {
            gsap.to(card, {
                ...state,
                duration,
                ease: 'power3.out',
            });

            return;
        }

        gsap.set(card, state);
    });
};

const moveMobileCreatorCard = async (
    direction: 'next' | 'previous' = 'next',
) => {
    const total = visibleCreators.value.length;

    if (!total) {
        return;
    }

    mobileActiveIndex.value =
        direction === 'next'
            ? (mobileActiveIndex.value + 1) % total
            : (mobileActiveIndex.value - 1 + total) % total;

    await renderMobileCarousel();
};

const handleMobileCardClick = async (index: number) => {
    if (index === mobileActiveIndex.value) {
        openCreatorDetails(visibleCreators.value[index] ?? null);

        return;
    }

    mobileActiveIndex.value = index;
    await renderMobileCarousel();
};

const teardownMobileCarousel = () => {
    mobileDraggable.forEach((draggable) => draggable.kill());
    mobileDraggable = [];
    mobileGsapContext?.revert();
    mobileGsapContext = null;
};

const setupMobileCarousel = async () => {
    if (!mobileGallery.value || mobileGsapContext) {
        return;
    }

    const [{ gsap }, { Draggable }] = await Promise.all([
        import('gsap'),
        import('gsap/Draggable'),
    ]);

    gsap.registerPlugin(Draggable);

    mobileGsapContext = gsap.context(() => {
        renderMobileCarousel(false);

        if (!mobileDragProxy.value || !mobileGallery.value) {
            return;
        }

        mobileDraggable = Draggable.create(mobileDragProxy.value, {
            type: 'x',
            trigger: mobileGallery.value,
            onDragEnd(this: { startX: number; x: number }) {
                const distance = this.x - this.startX;

                if (Math.abs(distance) > 36) {
                    moveMobileCreatorCard(distance < 0 ? 'next' : 'previous');
                }

                gsap.set(mobileDragProxy.value, { x: 0 });
            },
        });
    }, mobileGallery.value);
};

const handleMobileCarouselQuery = () => {
    if (mobileMediaQuery?.matches) {
        setupMobileCarousel();

        return;
    }

    teardownMobileCarousel();
};

const updateCreatorCards = async (nextCards: Creator[]) => {
    const slider = creatorSlider.value;

    if (
        !slider ||
        isAnimating ||
        nextCards.length !== creatorCards.value.length
    ) {
        return;
    }

    const [{ gsap }, { Flip }] = await Promise.all([
        import('gsap'),
        import('gsap/Flip'),
    ]);

    gsap.registerPlugin(Flip);

    const cards = slider.querySelectorAll('.creator-card');
    const state = Flip.getState(cards);
    creatorCards.value = nextCards;

    await nextTick();

    activeFlip?.kill();
    isAnimating = true;

    activeFlip = Flip.from(state, {
        targets: slider.querySelectorAll('.creator-card'),
        ease: 'sine.inOut',
        absolute: true,
        onComplete() {
            isAnimating = false;
            activeFlip = null;
        },
        onInterrupt() {
            isAnimating = false;
            activeFlip = null;
        },
    });
};

const moveCreatorCard = async (direction: 'next' | 'previous' = 'next') => {
    const nextCards = [...creatorCards.value];

    if (direction === 'next') {
        const frontCard = nextCards.pop();

        if (!frontCard) {
            return;
        }

        nextCards.unshift(frontCard);
    } else {
        const backCard = nextCards.shift();

        if (!backCard) {
            return;
        }

        nextCards.push(backCard);
    }

    await updateCreatorCards(nextCards);
};

const moveCreatorToFront = async (creatorName: string) => {
    const creatorIndex = creatorCards.value.findIndex(
        (creator) => creator.name === creatorName,
    );

    if (creatorIndex < 0 || creatorIndex === creatorCards.value.length - 1) {
        return;
    }

    await updateCreatorCards([
        ...creatorCards.value.slice(creatorIndex + 1),
        ...creatorCards.value.slice(0, creatorIndex + 1),
    ]);
};

const openCreatorDetails = (creator: Creator | null) => {
    if (!creator) {
        return;
    }

    selectedCreator.value = creator;
    isDetailFlipped.value = false;
    isDetailOpen.value = true;
};

const toggleCreatorDetails = () => {
    isDetailFlipped.value = !isDetailFlipped.value;
};

watch(isDetailOpen, (isOpen) => {
    if (!isOpen) {
        isDetailFlipped.value = false;
    }
});

const handleCreatorDeckClick = (event: MouseEvent) => {
    if (!isDesktopDeckRevealed.value) {
        return;
    }

    if (!(event.target instanceof Element)) {
        return;
    }

    const card = event.target.closest<HTMLElement>('.creator-card');

    if (!card) {
        return;
    }

    if (card.dataset.front === 'true') {
        openCreatorDetails(frontCreator.value);

        return;
    }

    if (card.dataset.creatorName) {
        moveCreatorToFront(card.dataset.creatorName);
    }
};

const clearRevealFallback = () => {
    window.removeEventListener('scroll', scheduleDesktopDeckRevealCheck);
    window.removeEventListener('resize', scheduleDesktopDeckRevealCheck);

    if (revealFallbackFrame !== null) {
        window.clearTimeout(revealFallbackFrame);
        revealFallbackFrame = null;
    }

    if (revealFallbackTimer !== null) {
        window.clearInterval(revealFallbackTimer);
        revealFallbackTimer = null;
    }
};

const revealDesktopDeck = (settleImmediately = false) => {
    isDesktopDeckRevealed.value = true;
    revealObserver?.disconnect();
    revealObserver = null;
    clearRevealFallback();

    if (revealTransitionTimer !== null) {
        window.clearTimeout(revealTransitionTimer);
    }

    if (settleImmediately) {
        isDesktopDeckRevealSettled.value = true;
        isMobileDeckRevealed.value = true;
        renderMobileCarousel(false);
        revealTransitionTimer = null;

        return;
    }

    if (!isMobileDeckRevealed.value) {
        isMobileDeckRevealed.value = true;
        renderMobileCarousel(true, 0.9);
    }

    revealTransitionTimer = window.setTimeout(() => {
        isDesktopDeckRevealSettled.value = true;
        revealTransitionTimer = null;
    }, 1300);
};

function sectionIsInRevealRange(section: HTMLElement | null): boolean {
    if (!section) {
        return false;
    }

    const sectionBounds = section.getBoundingClientRect();

    return (
        sectionBounds.top <= window.innerHeight * 0.82 &&
        sectionBounds.bottom >= window.innerHeight * 0.18
    );
}

function checkDesktopDeckReveal() {
    revealFallbackFrame = null;

    if (isDesktopDeckRevealed.value) {
        clearRevealFallback();

        return;
    }

    if (sectionIsInRevealRange(carouselSection.value)) {
        revealDesktopDeck();
    }
}

function scheduleDesktopDeckRevealCheck() {
    if (revealFallbackFrame !== null) {
        return;
    }

    revealFallbackFrame = window.setTimeout(checkDesktopDeckReveal, 50);
}

onMounted(() => {
    creatorSlider.value?.addEventListener('click', handleCreatorDeckClick);
    mobileMediaQuery = window.matchMedia('(max-width: 1023px)');
    mobileMediaQuery.addEventListener('change', handleMobileCarouselQuery);
    handleMobileCarouselQuery();

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        revealDesktopDeck(true);

        return;
    }

    if ('IntersectionObserver' in window) {
        revealObserver = new IntersectionObserver(
            ([entry]) => {
                if (entry?.isIntersecting) {
                    revealDesktopDeck();
                }
            },
            {
                rootMargin: '0px 0px -18% 0px',
                threshold: 0.2,
            },
        );

        if (carouselSection.value) {
            revealObserver.observe(carouselSection.value);
        }
    }

    window.addEventListener('scroll', scheduleDesktopDeckRevealCheck, {
        passive: true,
    });
    window.addEventListener('resize', scheduleDesktopDeckRevealCheck);
    revealFallbackTimer = window.setInterval(
        scheduleDesktopDeckRevealCheck,
        150,
    );
    scheduleDesktopDeckRevealCheck();
});

onBeforeUnmount(() => {
    creatorSlider.value?.removeEventListener('click', handleCreatorDeckClick);
    mobileMediaQuery?.removeEventListener('change', handleMobileCarouselQuery);
    revealObserver?.disconnect();
    clearRevealFallback();
    teardownMobileCarousel();
    activeFlip?.kill();

    if (revealTransitionTimer !== null) {
        window.clearTimeout(revealTransitionTimer);
    }
});
</script>

<template>
    <section
        ref="carouselSection"
        class="overflow-hidden bg-brand-neutral-900 py-20 text-white"
    >
        <div
            class="mx-auto grid max-w-7xl gap-10 px-5 sm:px-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-center"
        >
            <div
                class="flex flex-col items-start gap-7 lg:min-h-144 lg:justify-center"
            >
                <p class="text-sm font-bold text-brand-yellow-500 uppercase">
                    Creator carousel
                </p>
                <h2
                    class="max-w-3xl text-4xl leading-tight font-extrabold sm:text-5xl xl:text-6xl"
                >
                    Assets made by creators, teams, and studios.
                </h2>
            </div>

            <div
                class="relative -mx-5 h-[40rem] overflow-hidden sm:-mx-8 sm:h-[37rem] lg:hidden"
            >
                <div ref="mobileGallery" class="mobile-creator-gallery">
                    <article
                        v-for="(creator, index) in visibleCreators"
                        :key="creator.name"
                        class="mobile-creator-card flex flex-col overflow-hidden rounded-3xl border border-brand-neutral-900 bg-brand-neutral-0 text-brand-neutral-900 shadow-[5px_5px_0_0_var(--color-brand-neutral-900)]"
                        :aria-label="
                            index === mobileActiveIndex
                                ? `View ${creator.name} details`
                                : `Show ${creator.name}`
                        "
                        role="button"
                        tabindex="0"
                        @click="handleMobileCardClick(index)"
                        @keydown.enter.prevent="handleMobileCardClick(index)"
                        @keydown.space.prevent="handleMobileCardClick(index)"
                    >
                        <div
                            :style="{
                                backgroundImage: `url('${creator.imageUrl}')`,
                            }"
                            :class="[
                                'relative min-h-0 flex-1 bg-cover',
                                creator.position,
                            ]"
                        >
                            <div
                                class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-3 p-4"
                            >
                                <span
                                    class="rounded-full border border-brand-neutral-900 bg-brand-neutral-900 px-3 py-1.5 text-xs font-bold text-brand-neutral-0"
                                >
                                    {{ creator.category }}
                                </span>
                            </div>
                        </div>

                        <div :class="['min-h-44 p-5', creator.accent]">
                            <h4 class="text-3xl leading-tight font-extrabold">
                                {{ creator.name }}
                            </h4>
                            <p class="mt-5 font-semibold">Featured in</p>
                            <div class="mt-3 grid gap-2 text-sm font-bold">
                                <div class="flex items-center gap-2">
                                    <Play
                                        class="size-5 fill-brand-neutral-900"
                                    />
                                    {{ creator.metric }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <Music2
                                        class="size-5 fill-brand-neutral-900"
                                    />
                                    {{ creator.usage }}
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <div
                    class="absolute bottom-0 left-1/2 z-30 flex -translate-x-1/2 items-center justify-center gap-4"
                >
                    <button
                        type="button"
                        class="inline-flex min-h-12 items-center justify-center rounded-full border-2 border-brand-neutral-100 bg-brand-neutral-900 px-6 text-sm font-extrabold text-brand-neutral-100 shadow-[3px_3px_0_0_var(--color-brand-neutral-900)] transition hover:bg-brand-pink-400 hover:text-brand-neutral-900 focus:ring-2 focus:ring-brand-pink-400 focus:outline-none"
                        @click="moveMobileCreatorCard('previous')"
                    >
                        Prev
                    </button>
                    <button
                        type="button"
                        class="inline-flex min-h-12 items-center justify-center rounded-full border-2 border-brand-neutral-100 bg-brand-neutral-900 px-6 text-sm font-extrabold text-brand-neutral-100 shadow-[3px_3px_0_0_var(--color-brand-neutral-900)] transition hover:bg-brand-pink-400 hover:text-brand-neutral-900 focus:ring-2 focus:ring-brand-pink-400 focus:outline-none"
                        @click="moveMobileCreatorCard('next')"
                    >
                        Next
                    </button>
                </div>

                <div ref="mobileDragProxy" class="mobile-drag-proxy"></div>
            </div>

            <div class="relative hidden h-144 lg:block lg:h-156">
                <button
                    type="button"
                    class="absolute top-1/2 left-0 z-20 grid size-12 -translate-y-1/2 place-items-center rounded-full border-2 border-brand-yellow-500 bg-brand-neutral-900 text-brand-yellow-500 shadow-[3px_3px_0_0_var(--color-brand-yellow-500)] transition hover:bg-brand-yellow-500 hover:text-brand-neutral-900 focus:ring-2 focus:ring-brand-yellow-500 focus:outline-none sm:left-6"
                    aria-label="Previous creator card"
                    @click="moveCreatorCard('previous')"
                >
                    <ChevronLeft class="size-5" />
                </button>

                <div
                    ref="creatorSlider"
                    class="creator-slider"
                    :class="{
                        'is-deck-revealed': isDesktopDeckRevealed,
                        'is-deck-reveal-settled': isDesktopDeckRevealSettled,
                    }"
                >
                    <article
                        v-for="(creator, index) in creatorCards"
                        :key="creator.name"
                        class="creator-card flex flex-col overflow-hidden rounded-xl border border-brand-neutral-900 bg-brand-neutral-0 text-brand-neutral-900 shadow-[3px_3px_0_0_var(--color-brand-neutral-900)]"
                        :data-front="index === creatorCards.length - 1"
                        :data-creator-name="creator.name"
                        :aria-label="
                            index === creatorCards.length - 1
                                ? `View ${creator.name} details`
                                : `Show ${creator.name}`
                        "
                        role="button"
                        tabindex="0"
                        @keydown.enter.prevent="
                            index === creatorCards.length - 1
                                ? openCreatorDetails(creator)
                                : moveCreatorToFront(creator.name)
                        "
                        @keydown.space.prevent="
                            index === creatorCards.length - 1
                                ? openCreatorDetails(creator)
                                : moveCreatorToFront(creator.name)
                        "
                    >
                        <div
                            :style="{
                                backgroundImage: `url('${creator.imageUrl}')`,
                            }"
                            :class="[
                                'relative min-h-0 flex-1 bg-cover',
                                creator.position,
                            ]"
                        >
                            <div
                                class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-3 p-4"
                            >
                                <span
                                    class="rounded-full border border-brand-neutral-900 bg-brand-neutral-900 px-3 py-1.5 text-xs font-bold text-brand-neutral-0"
                                >
                                    {{ creator.category }}
                                </span>
                            </div>
                        </div>

                        <div :class="['min-h-48 p-6', creator.accent]">
                            <h4 class="text-3xl leading-tight font-extrabold">
                                {{ creator.name }}
                            </h4>
                            <p class="mt-6 font-semibold">Featured in</p>
                            <div class="mt-3 grid gap-2 text-sm font-bold">
                                <div class="flex items-center gap-2">
                                    <Play
                                        class="size-5 fill-brand-neutral-900"
                                    />
                                    {{ creator.metric }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <Music2
                                        class="size-5 fill-brand-neutral-900"
                                    />
                                    {{ creator.usage }}
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <button
                    type="button"
                    class="absolute top-1/2 right-0 z-20 grid size-12 -translate-y-1/2 place-items-center rounded-full border-2 border-brand-yellow-500 bg-brand-neutral-900 text-brand-yellow-500 shadow-[3px_3px_0_0_var(--color-brand-yellow-500)] transition hover:bg-brand-yellow-500 hover:text-brand-neutral-900 focus:ring-2 focus:ring-brand-yellow-500 focus:outline-none sm:right-6"
                    aria-label="Next creator card"
                    @click="moveCreatorCard('next')"
                >
                    <ChevronRight class="size-5" />
                </button>
            </div>
        </div>

        <Dialog :open="isDetailOpen" @update:open="isDetailOpen = $event">
            <DialogContent
                class="fixed inset-0 top-0 left-0 h-dvh max-h-none w-screen max-w-none translate-x-0 translate-y-0 overflow-y-auto border-0 bg-transparent p-0 text-brand-neutral-900 shadow-none **:data-[slot=dialog-close]:top-6 **:data-[slot=dialog-close]:right-6 **:data-[slot=dialog-close]:rounded-full **:data-[slot=dialog-close]:border **:data-[slot=dialog-close]:border-brand-neutral-900 **:data-[slot=dialog-close]:bg-brand-yellow-500 **:data-[slot=dialog-close]:p-2 **:data-[slot=dialog-close]:text-brand-neutral-900 **:data-[slot=dialog-close]:opacity-100 **:data-[slot=dialog-close]:shadow-[3px_3px_0_0_var(--color-brand-neutral-900)] sm:max-w-none"
            >
                <div
                    v-if="selectedCreator"
                    class="flex min-h-dvh items-center justify-center px-5 py-14 sm:px-8"
                >
                    <div
                        class="creator-detail-flip-shell lg:hidden"
                        :class="{ 'is-flipped': isDetailFlipped }"
                    >
                        <div
                            class="creator-detail-flip-card"
                            role="button"
                            tabindex="0"
                            :aria-label="
                                isDetailFlipped
                                    ? `Show ${selectedCreator.name} cover`
                                    : `Show ${selectedCreator.name} details`
                            "
                            :aria-pressed="isDetailFlipped"
                            @click="toggleCreatorDetails"
                            @keydown.enter.prevent="toggleCreatorDetails"
                            @keydown.space.prevent="toggleCreatorDetails"
                        >
                            <div
                                class="creator-detail-flip-face creator-detail-flip-face-front overflow-hidden rounded-3xl border border-brand-neutral-900 bg-brand-neutral-0 text-brand-neutral-900 shadow-[5px_5px_0_0_var(--color-brand-neutral-900)]"
                            >
                                <div
                                    :style="{
                                        backgroundImage: `url('${selectedCreator.imageUrl}')`,
                                    }"
                                    :class="[
                                        'relative h-full bg-cover',
                                        selectedCreator.position,
                                    ]"
                                >
                                    <div
                                        class="absolute inset-0 bg-linear-to-t from-brand-neutral-900/82 via-brand-neutral-900/8 to-transparent"
                                    />
                                    <span
                                        :class="[
                                            'absolute top-5 right-5 rounded-full border border-brand-neutral-900 px-3 py-1 text-xs font-extrabold text-brand-neutral-900',
                                            selectedCreator.accent,
                                        ]"
                                    >
                                        {{ selectedCreator.category }}
                                    </span>
                                    <div
                                        class="absolute inset-x-5 bottom-5 rounded-2xl border border-brand-neutral-900 bg-brand-neutral-0 p-5"
                                    >
                                        <h3
                                            class="text-3xl leading-none font-extrabold"
                                        >
                                            {{ selectedCreator.name }}
                                        </h3>
                                        <p
                                            class="mt-2 text-sm font-bold text-brand-neutral-600 uppercase"
                                        >
                                            Featured creator asset
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="creator-detail-flip-face creator-detail-flip-face-back flex flex-col overflow-y-auto rounded-3xl border border-brand-neutral-900 bg-brand-neutral-0 p-5 text-brand-neutral-900 shadow-[5px_5px_0_0_var(--color-brand-neutral-900)] sm:p-6"
                            >
                                <div
                                    :class="[
                                        'mb-5 h-2.5 rounded-full border border-brand-neutral-900',
                                        selectedCreator.accent,
                                    ]"
                                />

                                <DialogHeader class="text-left">
                                    <DialogTitle
                                        class="text-3xl leading-none font-extrabold text-brand-neutral-900 sm:text-4xl"
                                    >
                                        {{ selectedCreator.name }}
                                    </DialogTitle>
                                    <DialogDescription
                                        class="text-sm font-extrabold tracking-wide text-brand-neutral-600 uppercase"
                                    >
                                        {{ selectedCreator.category }}
                                    </DialogDescription>
                                </DialogHeader>

                                <p
                                    class="mt-5 text-base leading-7 font-medium text-brand-neutral-600"
                                >
                                    {{
                                        selectedCreator.details ??
                                        `${selectedCreator.name} is featured for creator-ready work with ${selectedCreator.metric} and ${selectedCreator.usage}.`
                                    }}
                                </p>

                                <div
                                    class="mt-auto grid gap-3 pt-6 sm:grid-cols-2"
                                >
                                    <div
                                        class="grid min-h-28 overflow-hidden rounded-xl border-t border-r-2 border-b-2 border-l border-brand-neutral-900 bg-brand-neutral-0 text-brand-neutral-900"
                                    >
                                        <div
                                            class="flex items-center gap-3 p-4"
                                        >
                                            <span
                                                :class="[
                                                    'grid size-10 shrink-0 place-items-center rounded-xl border border-brand-neutral-900',
                                                    selectedCreator.accent,
                                                ]"
                                            >
                                                <Play
                                                    class="size-4 fill-brand-neutral-900"
                                                />
                                            </span>
                                            <div>
                                                <p
                                                    class="text-xs font-bold text-brand-neutral-600 uppercase"
                                                >
                                                    Reach
                                                </p>
                                                <p
                                                    class="mt-1 text-lg font-extrabold"
                                                >
                                                    {{ selectedCreator.metric }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="grid min-h-28 overflow-hidden rounded-xl border-t border-r-2 border-b-2 border-l border-brand-neutral-900 bg-brand-neutral-0 text-brand-neutral-900"
                                    >
                                        <div
                                            class="flex items-center gap-3 p-4"
                                        >
                                            <span
                                                :class="[
                                                    'grid size-10 shrink-0 place-items-center rounded-xl border border-brand-neutral-900',
                                                    selectedCreator.accent,
                                                ]"
                                            >
                                                <Music2
                                                    class="size-4 fill-brand-neutral-900"
                                                />
                                            </span>
                                            <div>
                                                <p
                                                    class="text-xs font-bold text-brand-neutral-600 uppercase"
                                                >
                                                    Reuse
                                                </p>
                                                <p
                                                    class="mt-1 text-lg font-extrabold"
                                                >
                                                    {{ selectedCreator.usage }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="creator-detail-desktop-layout hidden w-full max-w-5xl items-center gap-5 lg:grid lg:grid-cols-[0.95fr_1.05fr] lg:gap-8"
                    >
                        <div
                            class="group relative h-136 origin-bottom-right -rotate-2 overflow-hidden rounded-3xl border border-brand-neutral-900 bg-brand-neutral-0 text-brand-neutral-900 sm:h-152 lg:h-160"
                        >
                            <div
                                :style="{
                                    backgroundImage: `url('${selectedCreator.imageUrl}')`,
                                }"
                                :class="[
                                    'h-full bg-cover',
                                    selectedCreator.position,
                                ]"
                            />
                            <div
                                class="absolute inset-0 bg-linear-to-t from-brand-neutral-900/82 via-brand-neutral-900/8 to-transparent"
                            />
                            <span
                                :class="[
                                    'absolute top-5 right-5 rounded-full border border-brand-neutral-900 px-3 py-1 text-xs font-extrabold text-brand-neutral-900',
                                    selectedCreator.accent,
                                ]"
                            >
                                {{ selectedCreator.category }}
                            </span>
                            <div
                                class="absolute inset-x-5 bottom-5 rounded-2xl border border-brand-neutral-900 bg-brand-neutral-0 p-5"
                            >
                                <h3
                                    class="text-3xl leading-none font-extrabold"
                                >
                                    {{ selectedCreator.name }}
                                </h3>
                                <p
                                    class="mt-2 text-sm font-bold text-brand-neutral-600 uppercase"
                                >
                                    Featured creator asset
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex h-136 origin-bottom-left rotate-2 flex-col rounded-3xl border border-brand-neutral-900 bg-brand-neutral-0 p-5 text-brand-neutral-900 sm:h-152 sm:p-6 lg:h-160"
                        >
                            <div
                                :class="[
                                    'mb-5 h-2.5 rounded-full border border-brand-neutral-900',
                                    selectedCreator.accent,
                                ]"
                            />

                            <DialogHeader class="text-left">
                                <DialogTitle
                                    class="text-3xl leading-none font-extrabold text-brand-neutral-900 sm:text-4xl"
                                >
                                    {{ selectedCreator.name }}
                                </DialogTitle>
                                <DialogDescription
                                    class="text-sm font-extrabold tracking-wide text-brand-neutral-600 uppercase"
                                >
                                    {{ selectedCreator.category }}
                                </DialogDescription>
                            </DialogHeader>

                            <p
                                class="mt-5 text-base leading-7 font-medium text-brand-neutral-600"
                            >
                                {{
                                    selectedCreator.details ??
                                    `${selectedCreator.name} is featured for creator-ready work with ${selectedCreator.metric} and ${selectedCreator.usage}.`
                                }}
                            </p>

                            <div class="mt-auto grid gap-3 pt-6 sm:grid-cols-2">
                                <div
                                    class="grid min-h-28 overflow-hidden rounded-xl border-t border-r-2 border-b-2 border-l border-brand-neutral-900 bg-brand-neutral-0 text-brand-neutral-900"
                                >
                                    <div class="flex items-center gap-3 p-4">
                                        <span
                                            :class="[
                                                'grid size-10 shrink-0 place-items-center rounded-xl border border-brand-neutral-900',
                                                selectedCreator.accent,
                                            ]"
                                        >
                                            <Play
                                                class="size-4 fill-brand-neutral-900"
                                            />
                                        </span>
                                        <div>
                                            <p
                                                class="text-xs font-bold text-brand-neutral-600 uppercase"
                                            >
                                                Reach
                                            </p>
                                            <p
                                                class="mt-1 text-lg font-extrabold"
                                            >
                                                {{ selectedCreator.metric }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="grid min-h-28 overflow-hidden rounded-xl border-t border-r-2 border-b-2 border-l border-brand-neutral-900 bg-brand-neutral-0 text-brand-neutral-900"
                                >
                                    <div class="flex items-center gap-3 p-4">
                                        <span
                                            :class="[
                                                'grid size-10 shrink-0 place-items-center rounded-xl border border-brand-neutral-900',
                                                selectedCreator.accent,
                                            ]"
                                        >
                                            <Music2
                                                class="size-4 fill-brand-neutral-900"
                                            />
                                        </span>
                                        <div>
                                            <p
                                                class="text-xs font-bold text-brand-neutral-600 uppercase"
                                            >
                                                Reuse
                                            </p>
                                            <p
                                                class="mt-1 text-lg font-extrabold"
                                            >
                                                {{ selectedCreator.usage }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </section>
</template>

<style scoped>
.mobile-creator-gallery {
    position: absolute;
    top: 1.5rem;
    inset-inline: 0;
    height: min(31rem, calc(100dvh - 10.5rem));
    touch-action: pan-y;
}

.mobile-creator-card {
    position: absolute;
    top: 0;
    left: 50%;
    width: clamp(16rem, 78vw, 19rem);
    height: min(31rem, calc(100dvh - 10.5rem));
    cursor: grab;
    will-change: transform, opacity;
}

.mobile-creator-card:active {
    cursor: grabbing;
}

.mobile-drag-proxy {
    visibility: hidden;
    position: absolute;
    width: 1px;
    height: 1px;
}

.creator-slider {
    position: absolute;
    width: min(300px, 68vw);
    height: 200px;
    perspective: 100px;
    top: 7rem;
    left: calc(50% - 40px);
    transform: translateX(-50%);
}

.creator-card {
    position: absolute;
    width: min(300px, 68vw);
    height: min(450px, 102vw);
    aspect-ratio: 2 / 3;
    background: transparent;
    cursor: pointer;
}

.creator-detail-flip-shell {
    width: min(calc(100vw - 2.5rem), 26rem, calc((100dvh - 7rem) * 2 / 3));
    aspect-ratio: 2 / 3;
    perspective: 1200px;
}

.creator-detail-flip-card {
    position: relative;
    width: 100%;
    height: 100%;
    cursor: pointer;
    outline: none;
    transform-style: preserve-3d;
    transition: transform 0.65s cubic-bezier(0.2, 0.7, 0.2, 1);
}

.creator-detail-flip-card:focus-visible {
    border-radius: 1.5rem;
    box-shadow: 0 0 0 4px var(--color-brand-yellow-500);
}

.creator-detail-flip-shell.is-flipped .creator-detail-flip-card {
    transform: rotateY(180deg);
}

.creator-detail-flip-face {
    position: absolute;
    inset: 0;
    backface-visibility: hidden;
}

.creator-detail-flip-face-back {
    transform: rotateY(180deg);
}

@media (min-width: 1024px) {
    .creator-card {
        left: 0;
        top: 0;
        opacity: 0;
        transition:
            top 900ms cubic-bezier(0.18, 0.9, 0.2, 1),
            left 900ms cubic-bezier(0.18, 0.9, 0.2, 1),
            opacity 520ms ease-out;
        will-change: top, left, opacity;
    }

    .creator-card:nth-child(5) {
        opacity: 1;
    }

    .creator-slider.is-deck-revealed .creator-card {
        opacity: 1;
    }

    .creator-slider.is-deck-revealed .creator-card:nth-child(5) {
        left: 0;
        top: 0;
        transition-delay: 0ms;
    }

    .creator-slider.is-deck-revealed .creator-card:nth-child(4) {
        left: 20px;
        top: -20px;
        transition-delay: 80ms;
    }

    .creator-slider.is-deck-revealed .creator-card:nth-child(3) {
        left: 40px;
        top: -40px;
        transition-delay: 160ms;
    }

    .creator-slider.is-deck-revealed .creator-card:nth-child(2) {
        left: 60px;
        top: -60px;
        transition-delay: 240ms;
    }

    .creator-slider.is-deck-revealed .creator-card:nth-child(1) {
        left: 80px;
        top: -80px;
        transition-delay: 320ms;
    }

    .creator-slider.is-deck-reveal-settled .creator-card {
        transition: none;
        transition-delay: 0ms;
        will-change: auto;
    }
}

@media (prefers-reduced-motion: reduce) {
    .creator-card {
        transition: none;
    }
}
</style>
