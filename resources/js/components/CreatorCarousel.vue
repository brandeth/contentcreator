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

const frontCreator = computed(
    () => creatorCards.value[creatorCards.value.length - 1] ?? null,
);

const creatorSlider = ref<HTMLElement | null>(null);
let activeFlip: { kill: () => void } | null = null;
let isAnimating = false;

watch(
    visibleCreators,
    (creators) => {
        creatorCards.value = [...creators];
    },
    { immediate: true },
);

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
    isDetailOpen.value = true;
};

const handleCreatorDeckClick = (event: MouseEvent) => {
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

onMounted(() => {
    creatorSlider.value?.addEventListener('click', handleCreatorDeckClick);
});

onBeforeUnmount(() => {
    creatorSlider.value?.removeEventListener('click', handleCreatorDeckClick);
    activeFlip?.kill();
});
</script>

<template>
    <section class="overflow-hidden bg-brand-neutral-900 py-20 text-white">
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

            <div class="relative h-144 lg:h-156">
                <button
                    type="button"
                    class="absolute top-1/2 left-0 z-20 grid size-12 -translate-y-1/2 place-items-center rounded-full border-2 border-brand-yellow-500 bg-brand-neutral-900 text-brand-yellow-500 shadow-[3px_3px_0_0_var(--color-brand-yellow-500)] transition hover:bg-brand-yellow-500 hover:text-brand-neutral-900 focus:ring-2 focus:ring-brand-yellow-500 focus:outline-none sm:left-6"
                    aria-label="Previous creator card"
                    @click="moveCreatorCard('previous')"
                >
                    <ChevronLeft class="size-5" />
                </button>

                <div ref="creatorSlider" class="creator-slider">
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
                        class="grid w-full max-w-5xl items-center gap-5 lg:grid-cols-[0.95fr_1.05fr] lg:gap-8"
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

.creator-card:nth-child(5) {
    left: 0;
    top: 0;
}

.creator-card:nth-child(4) {
    left: 20px;
    top: -20px;
}

.creator-card:nth-child(3) {
    left: 40px;
    top: -40px;
}

.creator-card:nth-child(2) {
    left: 60px;
    top: -60px;
}

.creator-card:nth-child(1) {
    left: 80px;
    top: -80px;
}
</style>
