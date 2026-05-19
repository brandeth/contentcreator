<script setup lang="ts">
import { Play, Sparkles } from 'lucide-vue-next';
import { computed } from 'vue';

type ShowcaseItem = {
    title: string;
    eyebrow: string;
    description: string;
    imageUrl?: string;
    videoUrl?: string;
};

const props = defineProps<{
    items: ShowcaseItem[];
}>();

const modelLogos = [
    'Flux',
    'Seedream',
    'Google NanoBanana',
    'OpenAI',
    'Luma AI',
    'Topaz Labs',
    'KlingAI',
    'Veo',
];

const marqueeLogos = [...modelLogos, ...modelLogos];

const showcaseItems = computed(() =>
    props.items
        .filter((item) => item.imageUrl || item.videoUrl)
        .slice(0, 5)
        .map((item, index) => ({
            ...item,
            frameClass:
                [
                    'md:h-112 md:w-68 lg:h-124 lg:w-80',
                    'md:h-88 md:w-52 lg:h-100 lg:w-60',
                    'md:h-104 md:w-76 lg:h-116 lg:w-88',
                    'md:h-88 md:w-52 lg:h-100 lg:w-60',
                    'md:h-112 md:w-68 lg:h-124 lg:w-80',
                ][index] ?? 'md:h-100 md:w-64',
            offsetClass:
                ['md:mt-2', 'md:mt-14', 'md:mt-7', 'md:mt-14', 'md:mt-2'][
                    index
                ] ?? 'md:mt-8',
        })),
);
</script>

<template>
    <section
        class="relative isolate overflow-hidden bg-brand-neutral-0 py-16 text-brand-neutral-900 sm:py-20"
    >
        <div class="relative z-10 mx-auto max-w-7xl px-5 sm:px-8">
            <div class="relative mx-auto max-w-6xl overflow-hidden">
                <div
                    class="pointer-events-none absolute inset-y-0 left-0 z-10 w-16 bg-linear-to-r from-brand-neutral-0 to-transparent sm:w-28"
                />
                <div
                    class="pointer-events-none absolute inset-y-0 right-0 z-10 w-16 bg-linear-to-l from-brand-neutral-0 to-transparent sm:w-28"
                />

                <div class="model-marquee flex w-max items-center gap-10 py-2">
                    <div
                        v-for="(logo, index) in marqueeLogos"
                        :key="`${logo}-${index}`"
                        class="flex min-w-max items-center gap-3 text-xl font-extrabold tracking-normal text-brand-neutral-900/72 sm:text-2xl"
                        aria-hidden="true"
                    >
                        <span
                            class="grid size-8 place-items-center rounded-full border border-brand-neutral-900/12 bg-brand-neutral-900/5 text-brand-yellow-500"
                        >
                            <Sparkles class="size-4" />
                        </span>
                        {{ logo }}
                    </div>
                </div>
            </div>

            <div
                class="mt-14 flex gap-4 overflow-x-auto pb-3 md:mt-18 md:justify-center md:overflow-visible md:pb-0 lg:gap-5"
            >
                <article
                    v-for="item in showcaseItems"
                    :key="item.title"
                    :style="
                        item.imageUrl
                            ? { backgroundImage: `url('${item.imageUrl}')` }
                            : undefined
                    "
                    :class="[
                        'group relative h-96 w-68 shrink-0 overflow-hidden rounded-3xl border border-brand-neutral-900 bg-brand-neutral-900 bg-cover bg-center text-white shadow-[5px_5px_0_0_var(--color-brand-neutral-900)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[7px_7px_0_0_var(--color-brand-neutral-900)]',
                        item.frameClass,
                        item.offsetClass,
                    ]"
                >
                    <video
                        v-if="item.videoUrl"
                        class="absolute inset-0 size-full object-cover"
                        :src="item.videoUrl"
                        autoplay
                        muted
                        loop
                        playsinline
                        preload="metadata"
                    />

                    <div
                        class="absolute inset-0 bg-linear-to-t from-brand-neutral-900/88 via-brand-neutral-900/14 to-transparent opacity-[.92] transition group-hover:opacity-75"
                    />

                    <div
                        class="absolute inset-x-0 top-0 z-10 flex items-center justify-between gap-3 p-4"
                    >
                        <span
                            class="max-w-[calc(100%-3rem)] truncate rounded-full border border-white/20 bg-white/16 px-3 py-1 text-xs font-bold text-white backdrop-blur"
                        >
                            {{ item.eyebrow }}
                        </span>
                        <span
                            class="grid size-10 shrink-0 place-items-center rounded-full border border-white/15 bg-white/16 text-white backdrop-blur"
                        >
                            <Play
                                v-if="item.videoUrl"
                                class="size-4 fill-current"
                            />
                            <Sparkles v-else class="size-4" />
                        </span>
                    </div>

                    <div class="absolute inset-x-0 bottom-0 z-10 p-5">
                        <h3
                            class="text-2xl leading-tight font-extrabold drop-shadow-[0_2px_12px_rgba(0,0,0,.55)]"
                        >
                            {{ item.title }}
                        </h3>
                        <p
                            class="mt-3 line-clamp-2 text-sm leading-6 text-white/78 drop-shadow-[0_2px_10px_rgba(0,0,0,.5)]"
                        >
                            {{ item.description }}
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>

<style scoped>
.model-marquee {
    animation: model-marquee 34s linear infinite;
}

@keyframes model-marquee {
    from {
        transform: translateX(0);
    }

    to {
        transform: translateX(-50%);
    }
}

@media (prefers-reduced-motion: reduce) {
    .model-marquee {
        animation: none;
    }
}
</style>
