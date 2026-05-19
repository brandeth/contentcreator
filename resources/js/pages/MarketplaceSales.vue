<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Check,
    Clapperboard,
    Coins,
    Image,
    Layers3,
    Mic2,
    Play,
    PlugZap,
    Search,
    Sparkles,
    Star,
    Wand2,
    Zap,
} from 'lucide-vue-next';
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onBeforeUpdate,
    onMounted,
    ref,
    watch,
} from 'vue';
import type { ComponentPublicInstance } from 'vue';
import CreatorCarousel from '@/components/CreatorCarousel.vue';
import CreatorWorkflowMotion from '@/components/CreatorWorkflowMotion.vue';
import PinnedStoryPanels from '@/components/PinnedStoryPanels.vue';

const tools = [
    {
        icon: Image,
        title: 'AI image tools',
        description:
            'Create thumbnails, ad creatives, character shots, and product visuals without bouncing between model sites.',
        tint: 'bg-brand-teal-400',
        items: ['Nano Banana', 'Ideogram', 'Image editing'],
    },
    {
        icon: Clapperboard,
        title: 'AI video tools',
        description:
            'Generate cinematic clips, social ads, effects shots, b-roll, and concept scenes from one workflow.',
        tint: 'bg-brand-blue-400',
        items: ['Veo', 'KlingAI', 'Seedance'],
    },
    {
        icon: Mic2,
        title: 'AI audio tools',
        description:
            'Produce voiceovers, voice clones, music, and sound effects for publish-ready creator projects.',
        tint: 'bg-brand-pink-400',
        items: ['ElevenLabs', 'Suno', 'Voice swaps'],
    },
    {
        icon: PlugZap,
        title: 'Editing plugins',
        description:
            'Bring AI generation closer to the timeline with workflow surfaces for Premiere Pro and DaVinci Resolve.',
        tint: 'bg-brand-yellow-500',
        items: ['Premiere Pro', 'DaVinci Resolve', 'Timeline ready'],
    },
];

const bentoItems = [
    {
        title: 'Free the creator within',
        eyebrow: 'ImageGen',
        description:
            'Make thumbnail, ad, and product concepts without leaving the marketplace.',
        icon: Image,
        accent: 'bg-brand-neutral-100',
        layout: 'lg:col-span-4 lg:row-span-2',
        visual: 'rings',
        imageUrl:
            'https://plus.unsplash.com/premium_photo-1664272436668-78437b92929e?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    },
    {
        title: 'Motion',
        eyebrow: 'VideoGen',
        description:
            'Generate hooks, reveals, ads, b-roll, and cinematic shorts.',
        icon: Clapperboard,
        accent: 'bg-brand-yellow-500',
        layout: 'lg:col-span-5 lg:row-span-2',
        visual: 'motion',
        imageUrl:
            'https://images.unsplash.com/flagged/photo-1572392640988-ba48d1a74457?q=80&w=764&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        videoUrl: 'https://www.pexels.com/download/video/34013104/',
    },
    {
        title: 'Tool library',
        eyebrow: 'Workspace',
        description:
            'Image, video, voice, audio, plugin, and editing surfaces.',
        icon: PlugZap,
        accent: 'bg-white',
        layout: 'lg:col-span-3 lg:row-span-3',
        visual: 'icons',
        imageUrl:
            'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=745&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    },
    {
        title: 'Tap. Prompt. Ta-da!',
        eyebrow: 'PromptEdit',
        description: 'Turn one idea into a complete creator asset set.',
        icon: Wand2,
        accent: 'bg-brand-blue-400',
        layout: 'lg:col-span-3 lg:row-span-3',
        visual: 'studio',
        imageUrl:
            'https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        videoUrl: 'https://www.pexels.com/download/video/9153299/',
    },
    {
        title: 'PromptEdit',
        eyebrow: 'Marketplace',
        description: 'One credit wallet for every generated asset.',
        icon: Sparkles,
        accent: 'bg-brand-pink-400',
        layout: 'lg:col-span-6 lg:row-span-3',
        visual: 'brand',
        imageUrl:
            'https://plus.unsplash.com/premium_photo-1695405363183-e55554168063?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    },
    {
        title: 'Brand kits',
        eyebrow: 'Assets',
        description:
            'Logos, swatches, stickers, and repeatable visual systems.',
        icon: Layers3,
        accent: 'bg-brand-teal-400',
        layout: 'lg:col-span-3 lg:row-span-2',
        visual: 'logos',
        imageUrl:
            'https://images.unsplash.com/photo-1634986666676-ec8fd927c23d?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        videoUrl: 'https://www.pexels.com/download/video/7598768/',
    },
    {
        title: 'Ideas',
        eyebrow: 'Campaigns',
        description: 'Storyboard posts and launch directions from one prompt.',
        icon: Sparkles,
        accent: 'bg-brand-yellow-500',
        layout: 'lg:col-span-3 lg:row-span-2',
        visual: 'cards',
        imageUrl:
            'https://images.unsplash.com/photo-1641391503184-a2131018701b?q=80&w=880&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        videoUrl: 'https://www.pexels.com/download/video/18069233/',
    },
    {
        title: 'Voice and sound',
        eyebrow: 'AudioGen',
        description: 'Voiceovers, music beds, impacts, and loops.',
        icon: Mic2,
        accent: 'bg-brand-neutral-100',
        layout: 'lg:col-span-3 lg:row-span-2',
        visual: 'wave',
        imageUrl: undefined,
    },
    {
        title: 'Merch mockups',
        eyebrow: 'ImageEdit',
        description: 'Product mockups for creator drops and launch pages.',
        icon: Image,
        accent: 'bg-brand-teal-400',
        layout: 'lg:col-span-3 lg:row-span-2',
        visual: 'mockup',
        imageUrl: undefined,
        videoUrl: 'https://www.pexels.com/download/video/5405659/',
    },
    {
        title: 'Launch posters',
        eyebrow: 'Templates',
        description:
            'Campaign visuals ready for socials, ads, and storefronts.',
        icon: Clapperboard,
        accent: 'bg-brand-neutral-900 text-white',
        layout: 'lg:col-span-3 lg:row-span-2',
        visual: 'posters',
        imageUrl: undefined,
    },
];

const activeHeroTool = ref(0);

const heroToolDemos = [
    {
        tabTitle: 'AI image tools',
        search: 'Product ad',
        running: '3 running',
        cards: [
            {
                label: 'Nano Banana',
                shortLabel: 'Nano',
                title: 'Studio reveal',
                mediaType: 'image',
                url: 'https://plus.unsplash.com/premium_photo-1726869694722-66df5f9621b8?q=80&w=1079&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            },
            {
                label: 'Thumbnail',
                shortLabel: 'Thumb',
                title: 'Launch pack',
                mediaType: 'image',
                url: 'https://plus.unsplash.com/premium_photo-1683140802177-372401d5c783?q=80&w=1198&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            },
        ],
        bars: [
            'w-full bg-brand-yellow-500',
            'w-4/5 bg-brand-teal-400',
            'w-2/3 bg-brand-pink-400',
        ],
    },
    {
        tabTitle: 'AI video tools',
        search: 'Veo scene',
        running: '2 rendering',
        cards: [
            {
                label: 'Veo scene',
                shortLabel: 'Veo',
                title: 'Motion ad',
                mediaType: 'video',
                url: 'https://www.pexels.com/download/video/6976218/',
            },
            {
                label: 'B-roll',
                shortLabel: 'B-roll',
                title: 'Creator short',
                mediaType: 'video',
                url: 'https://www.pexels.com/download/video/7778853/',
            },
        ],
        bars: [
            'w-11/12 bg-brand-blue-400',
            'w-3/4 bg-brand-yellow-500',
            'w-1/2 bg-brand-teal-400',
        ],
    },
    {
        tabTitle: 'AI audio tools',
        search: 'Voiceover',
        running: '4 mixing',
        cards: [
            {
                label: 'ElevenLabs',
                shortLabel: 'Eleven',
                title: 'Voiceover set',
                mediaType: 'video',
                url: 'https://www.pexels.com/download/video/4540152/',
            },
            {
                label: 'Sound bed',
                shortLabel: 'Sound',
                title: 'Audio pack',
                mediaType: 'video',
                url: 'https://www.pexels.com/download/video/12336865/',
            },
        ],
        bars: [
            'w-5/6 bg-brand-pink-400',
            'w-3/4 bg-brand-teal-400',
            'w-2/5 bg-brand-yellow-500',
        ],
    },
];

const activeHeroDemo = computed(() => heroToolDemos[activeHeroTool.value]);
const heroDemoVideos = ref<HTMLVideoElement[]>([]);

const featuredCreators = [
    {
        name: 'Mina Vale',
        category: 'AI portraits',
        metric: '4.8M generated views',
        usage: '18k creator remixes',
        imageUrl:
            'https://plus.unsplash.com/premium_photo-1664272436668-78437b92929e?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        accent: 'bg-brand-yellow-500',
        position: 'bg-center',
    },
    {
        name: 'Studio North',
        category: 'Video hooks',
        metric: '12M short-form views',
        usage: '44k saved templates',
        imageUrl:
            'https://images.unsplash.com/flagged/photo-1572392640988-ba48d1a74457?q=80&w=764&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        accent: 'bg-brand-teal-400',
        position: 'bg-center',
    },
    {
        name: 'Cleo Render',
        category: 'Launch visuals',
        metric: '31k campaign exports',
        usage: '9k asset packs',
        imageUrl:
            'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=745&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        accent: 'bg-brand-pink-400',
        position: 'bg-center',
    },
    {
        name: 'Ari Motion',
        category: 'B-roll systems',
        metric: '7.2M ad impressions',
        usage: '26k rendered clips',
        imageUrl:
            'https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        accent: 'bg-brand-blue-400',
        position: 'bg-center',
    },
    {
        name: 'Kota Pack',
        category: 'Brand kits',
        metric: '19k reusable assets',
        usage: '61k prompt runs',
        imageUrl:
            'https://images.unsplash.com/photo-1634986666676-ec8fd927c23d?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        accent: 'bg-brand-yellow-500',
        position: 'bg-center',
    },
    {
        name: 'Luma Stack',
        category: 'Template drops',
        metric: '82k saved edits',
        usage: '14k creator projects',
        imageUrl:
            'https://images.unsplash.com/photo-1641391503184-a2131018701b?q=80&w=880&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        accent: 'bg-brand-teal-400',
        position: 'bg-center',
    },
];

const comparisons = [
    {
        icon: Coins,
        need: 'Pricing model',
        promptEdit: 'One credit wallet',
        separateTools: 'Multiple subscriptions',
        detail: 'Buy credits once and spend them across the marketplace.',
        badge: 'No lock-in',
        accent: 'bg-brand-yellow-500',
    },
    {
        icon: Layers3,
        need: 'Creator assets',
        promptEdit: 'AI tools plus templates',
        separateTools: 'Usually one tool category',
        detail: 'Generation tools sit beside templates, LUTs, and assets.',
        badge: 'Assets included',
        accent: 'bg-brand-teal-400',
    },
    {
        icon: Zap,
        need: 'Workflow',
        promptEdit: 'Generate, save, and reuse',
        separateTools: 'Export and re-upload manually',
        detail: 'Keep ideas, outputs, and reusable pieces in one flow.',
        badge: 'Reusable',
        accent: 'bg-brand-pink-400',
    },
    {
        icon: Search,
        need: 'Learning curve',
        promptEdit: 'Consistent UI',
        separateTools: 'New interface every tool',
        detail: 'Creators learn one surface instead of chasing every model UI.',
        badge: 'Familiar',
        accent: 'bg-brand-blue-400',
    },
];

const stats = [
    { value: '100k+', label: 'creator assets' },
    { value: '20+', label: 'AI tool categories' },
    { value: '0', label: 'required subscriptions' },
];

type HeroTitleSplit = {
    words: Element[];
    lines?: Element[];
    revert: () => void;
};

const testimonials = [
    {
        quote: 'My videos would not look half as good without the templates. I lean on them for every edit.',
        name: 'Matt Lilley',
        role: 'ContentCreator.com student',
    },
    {
        quote: 'We got more editing work done in three hours than we normally do in three days.',
        name: 'Hilda Schlueter',
        role: 'Online course creator',
    },
    {
        quote: 'It is a fantastic resource. I do not need another digital asset subscription.',
        name: 'Brady Hales',
        role: 'ContentCreator.com student',
    },
];

const faqs = [
    {
        question: 'Do I need a subscription to use PromptEdit?',
        answer: 'No. You can buy credits and use them across supported AI tools. A subscription can add monthly credits and creator asset access, but the core promise is pay as you go.',
    },
    {
        question:
            'Are the AI results lower quality than using each model directly?',
        answer: 'No. The page should position PromptEdit as a simpler way to access the same generation categories through one creator-friendly interface.',
    },
    {
        question: 'Can I use what I create commercially?',
        answer: 'The sales page should answer this clearly near purchase points: creations and downloaded assets are intended for commercial creator projects, subject to the current terms.',
    },
    {
        question: 'Why include templates with AI tools?',
        answer: 'AI generation gets the asset made. Templates, LUTs, effects, and sounds help creators finish the actual video faster.',
    },
];

const activeFaqIndex = ref<number | null>(0);
const heroSection = ref<HTMLElement | null>(null);
const heroEyebrow = ref<HTMLElement | null>(null);
const heroTitle = ref<HTMLElement | null>(null);
const heroSummary = ref<HTMLElement | null>(null);
const heroActions = ref<HTMLElement | null>(null);
const heroStats = ref<HTMLElement | null>(null);
const heroDemoPanel = ref<HTMLElement | null>(null);
const toolsSection = ref<HTMLElement | null>(null);
const toolsSectionVisible = ref(false);
const pricingSection = ref<HTMLElement | null>(null);
const pricingSectionVisible = ref(false);
let toolsObserver: IntersectionObserver | null = null;
let pricingObserver: IntersectionObserver | null = null;
let heroAnimationContext: { revert: () => void } | null = null;
let heroTitleSplit: HeroTitleSplit | null = null;
let heroSummarySplit: HeroTitleSplit | null = null;
let pageMounted = false;

const toggleFaq = (index: number) => {
    activeFaqIndex.value = activeFaqIndex.value === index ? null : index;
};

const setHeroDemoVideoRef = (
    element: Element | ComponentPublicInstance | null,
) => {
    if (element instanceof HTMLVideoElement) {
        heroDemoVideos.value.push(element);
    }
};

const syncHeroDemoVideos = async () => {
    await nextTick();

    heroDemoVideos.value.forEach((video) => {
        const tabIndex = Number(video.dataset.heroTabIndex);

        if (tabIndex === activeHeroTool.value) {
            void video.play().catch(() => {
                // Muted autoplay can still be denied; retry on the next tab change.
            });

            return;
        }

        video.pause();
    });
};

const revealPricingSection = () => {
    pricingSectionVisible.value = true;
    pricingObserver?.disconnect();
    pricingObserver = null;
};

const revealToolsSection = () => {
    toolsSectionVisible.value = true;
    toolsObserver?.disconnect();
    toolsObserver = null;
};

const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const revealHeroImmediately = () => {
    heroEyebrow.value?.classList.remove('opacity-0');
    heroTitle.value?.classList.remove('opacity-0');
    heroSummary.value?.classList.remove('opacity-0');
    heroActions.value?.classList.remove('opacity-0');
    heroStats.value?.classList.remove('opacity-0');
    heroDemoPanel.value?.classList.remove('opacity-0');
};

const animateHero = async () => {
    if (prefersReducedMotion()) {
        revealHeroImmediately();

        return;
    }

    await Promise.all([document.fonts?.ready ?? Promise.resolve(), nextTick()]);

    if (!pageMounted || !heroSection.value || !heroTitle.value) {
        return;
    }

    const [{ gsap }, { SplitText }] = await Promise.all([
        import('gsap'),
        import('gsap/SplitText'),
    ]);

    if (!pageMounted || !heroSection.value || !heroTitle.value) {
        return;
    }

    gsap.registerPlugin(SplitText);

    heroAnimationContext = gsap.context(() => {
        const title = heroTitle.value;
        const eyebrow = heroEyebrow.value;
        const summary = heroSummary.value;
        const actions = heroActions.value;
        const statsPanel = heroStats.value;
        const panel = heroDemoPanel.value;

        if (
            !title ||
            !eyebrow ||
            !summary ||
            !actions ||
            !statsPanel ||
            !panel
        ) {
            return;
        }

        const actionItems = Array.from(
            actions.querySelectorAll<HTMLElement>('.hero-action-item'),
        );
        const statItems = Array.from(
            statsPanel.querySelectorAll<HTMLElement>('.hero-stat-item'),
        );
        const demoItems = Array.from(
            panel.querySelectorAll<HTMLElement>('.hero-demo-entrance-item'),
        );
        const clearDemoAnimationProps = () => {
            actions.classList.remove('opacity-0');
            gsap.set(
                [
                    eyebrow,
                    title,
                    summary,
                    actions,
                    ...actionItems,
                    statsPanel,
                    ...statItems,
                    panel,
                    ...demoItems,
                ],
                {
                    clearProps: 'opacity,visibility,transform',
                },
            );
        };

        heroEyebrow.value?.classList.remove('opacity-0');
        heroTitle.value?.classList.remove('opacity-0');
        heroSummary.value?.classList.remove('opacity-0');
        heroStats.value?.classList.remove('opacity-0');
        heroDemoPanel.value?.classList.remove('opacity-0');
        gsap.set([eyebrow, title, summary, statsPanel, panel], {
            clearProps: 'opacity,visibility,transform',
        });

        if (
            actionItems.length > 0 ||
            statItems.length > 0 ||
            demoItems.length > 0
        ) {
            gsap.set([...actionItems, ...statItems, ...demoItems], {
                clearProps: 'opacity,visibility,transform',
            });
        }

        const titleSplit = SplitText.create(title, {
            type: 'words',
            wordsClass: 'hero-title-word word++',
            ignore: 'sup',
        });
        const summarySplit = SplitText.create(summary, {
            type: 'lines',
            linesClass: 'hero-summary-line line++',
        });
        heroTitleSplit = titleSplit;
        heroSummarySplit = summarySplit;

        const timeline = gsap.timeline({
            defaults: { ease: 'power3.out' },
            onComplete: clearDemoAnimationProps,
            onInterrupt: clearDemoAnimationProps,
        });

        timeline
            .from(
                panel,
                {
                    y: 16,
                    scale: 0.985,
                    opacity: 0,
                    duration: 0.38,
                },
                0,
            )
            .from(eyebrow, {
                y: 12,
                scale: 0.985,
                opacity: 0,
                duration: 0.28,
                ease: 'power2.out',
            })
            .from(
                titleSplit.words,
                {
                    y: -100,
                    opacity: 0,
                    rotation: 'random(-80, 80)',
                    stagger: 0.1,
                    duration: 1,
                    ease: 'back',
                },
                '-=0.06',
            )
            .from(
                summarySplit.lines ?? [],
                {
                    y: 14,
                    opacity: 0,
                    stagger: 0.045,
                    duration: 0.34,
                },
                '-=0.2',
            )
            .set(actions, { opacity: 1 }, '-=0.08')
            .fromTo(
                actionItems,
                {
                    y: 18,
                    opacity: 0,
                    scale: 0.96,
                },
                {
                    y: 0,
                    opacity: 1,
                    scale: 1,
                    stagger: 0.08,
                    duration: 0.42,
                    ease: 'back.out(1.35)',
                    clearProps: 'opacity,transform',
                },
                '-=0.08',
            )
            .from(
                statItems,
                {
                    y: 10,
                    opacity: 0,
                    stagger: 0.045,
                    duration: 0.28,
                },
                '-=0.12',
            );

        if (demoItems.length > 0) {
            timeline.fromTo(
                demoItems,
                {
                    y: 22,
                    opacity: 0,
                    visibility: 'hidden',
                },
                {
                    y: 0,
                    opacity: 1,
                    visibility: 'visible',
                    stagger: 0.035,
                    duration: 0.3,
                    ease: 'power2.out',
                    clearProps: 'opacity,visibility,transform',
                },
                0.08,
            );
        }
    }, heroSection.value);
};

onMounted(() => {
    pageMounted = true;
    void animateHero();
    void syncHeroDemoVideos();

    if (prefersReducedMotion()) {
        revealToolsSection();
        revealPricingSection();

        return;
    }

    toolsObserver = new IntersectionObserver(
        ([entry]) => {
            if (entry?.isIntersecting) {
                revealToolsSection();
            }
        },
        {
            rootMargin: '0px 0px -10% 0px',
            threshold: 0.2,
        },
    );

    pricingObserver = new IntersectionObserver(
        ([entry]) => {
            if (entry?.isIntersecting) {
                revealPricingSection();
            }
        },
        {
            rootMargin: '0px 0px -12% 0px',
            threshold: 0.25,
        },
    );

    if (toolsSection.value) {
        toolsObserver.observe(toolsSection.value);
    }

    if (pricingSection.value) {
        pricingObserver.observe(pricingSection.value);
    }
});

onBeforeUpdate(() => {
    heroDemoVideos.value = [];
});

watch(activeHeroTool, () => {
    void syncHeroDemoVideos();
});

onBeforeUnmount(() => {
    pageMounted = false;
    heroAnimationContext?.revert();
    heroTitleSplit?.revert();
    heroSummarySplit?.revert();
    toolsObserver?.disconnect();
    pricingObserver?.disconnect();
});
</script>

<template>
    <Head title="PromptEdit Creator Marketplace">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin="anonymous"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
        />
    </Head>

    <main
        class="min-h-screen overflow-x-hidden bg-brand-neutral-100 font-['Poppins'] text-brand-neutral-900"
    >
        <header
            class="sticky top-0 z-40 border-b border-brand-neutral-900/10 bg-brand-neutral-100/90 backdrop-blur"
        >
            <nav
                class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-5 sm:px-8"
            >
                <Link
                    href="/marketplace"
                    class="flex items-center gap-3 font-bold tracking-normal"
                >
                    <span
                        class="grid size-9 place-items-center rounded-full border border-brand-neutral-900 bg-brand-yellow-500 shadow-[2px_2px_0_0_#2e1401]"
                    >
                        <Sparkles class="size-4" />
                    </span>
                    <span>PromptEdit</span>
                </Link>

                <div
                    class="hidden items-center gap-8 text-sm font-medium text-brand-neutral-600 md:flex"
                >
                    <a href="#tools" class="hover:text-brand-neutral-900"
                        >Tools</a
                    >
                    <a href="#pricing" class="hover:text-brand-neutral-900"
                        >Pricing</a
                    >
                    <a href="#faq" class="hover:text-brand-neutral-900">FAQ</a>
                </div>

                <a
                    href="#pricing"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full bg-brand-neutral-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-pink-700"
                >
                    Start creating
                    <ArrowRight class="size-4" />
                </a>
            </nav>
        </header>

        <section
            ref="heroSection"
            class="relative overflow-hidden border-b border-brand-neutral-900/10"
        >
            <div
                class="mx-auto grid w-full max-w-7xl items-center gap-10 px-5 py-12 sm:px-8 md:py-16 lg:grid-cols-[0.95fr_1.05fr] lg:py-20 xl:min-h-[calc(100vh-4rem)] xl:py-16"
            >
                <div class="max-w-2xl">
                    <div
                        ref="heroEyebrow"
                        class="mb-6 inline-flex items-center gap-2 rounded-full border border-brand-neutral-900 bg-white px-4 py-2 text-sm font-semibold opacity-0 shadow-[2px_2px_0_0_#2e1401]"
                    >
                        <Star
                            class="size-4 fill-brand-yellow-500 text-brand-neutral-900"
                        />
                        One marketplace for modern creators
                    </div>

                    <h1
                        ref="heroTitle"
                        class="text-4xl leading-[1.08] font-extrabold tracking-normal text-brand-neutral-900 opacity-0 sm:text-5xl xl:text-6xl"
                    >
                        Create AI content from one place.
                    </h1>

                    <p
                        ref="heroSummary"
                        class="mt-6 max-w-xl text-lg leading-8 text-brand-neutral-600 opacity-0"
                    >
                        PromptEdit gives creators access to AI video, image,
                        voiceover, template, audio, and editing tools without
                        juggling tabs, subscriptions, or scattered workflows.
                    </p>

                    <div
                        ref="heroActions"
                        class="mt-8 flex flex-col gap-3 opacity-0 sm:flex-row"
                    >
                        <a
                            href="#pricing"
                            class="hero-action-item inline-flex min-h-12 items-center justify-center gap-2 rounded-full bg-brand-yellow-500 px-6 py-3 font-bold text-brand-neutral-900 shadow-[inset_0_0_0_1px_#2e1401,3px_3px_0_0_#2e1401] transition hover:shadow-[inset_0_0_0_1px_#2e1401,5px_5px_0_0_#2e1401]"
                        >
                            Get started today
                            <ArrowRight class="size-5" />
                        </a>
                        <a
                            href="#tools"
                            class="hero-action-item inline-flex min-h-12 items-center justify-center rounded-full border border-brand-neutral-900 bg-white px-6 py-3 font-bold text-brand-neutral-900 transition hover:bg-brand-neutral-100"
                        >
                            Explore the marketplace
                        </a>
                    </div>

                    <div
                        ref="heroStats"
                        class="mt-10 grid max-w-xl grid-cols-3 gap-3 opacity-0"
                    >
                        <div
                            v-for="stat in stats"
                            :key="stat.label"
                            class="hero-stat-item border-l border-brand-neutral-900/20 pl-4"
                        >
                            <div
                                class="hero-stat-value text-2xl font-extrabold"
                            >
                                {{ stat.value }}
                            </div>
                            <div
                                class="text-sm font-medium text-brand-neutral-600"
                            >
                                {{ stat.label }}
                            </div>
                        </div>
                    </div>
                </div>

                <div ref="heroDemoPanel" class="relative opacity-0">
                    <div
                        class="rounded-4xl border border-brand-neutral-900 bg-white p-3 shadow-[8px_8px_0_0_#2e1401]"
                    >
                        <div
                            class="overflow-hidden rounded-[1.4rem] border border-brand-neutral-900 bg-brand-neutral-900 text-white"
                        >
                            <div
                                class="hero-demo-entrance-group flex items-center justify-between border-b border-white/15 px-4 py-3"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        class="hero-demo-entrance-item size-3 rounded-full bg-brand-pink-400"
                                    />
                                    <span
                                        class="hero-demo-entrance-item size-3 rounded-full bg-brand-yellow-500"
                                    />
                                    <span
                                        class="hero-demo-entrance-item size-3 rounded-full bg-brand-teal-400"
                                    />
                                </div>
                                <div
                                    class="hero-demo-entrance-item rounded-full bg-white/10 px-3 py-1 text-xs font-semibold"
                                >
                                    2,450 credits
                                </div>
                            </div>

                            <div
                                class="grid gap-4 p-4 md:grid-cols-[0.48fr_1fr]"
                            >
                                <aside
                                    class="hero-demo-entrance-group rounded-2xl bg-white/8 p-3"
                                >
                                    <div
                                        class="hero-demo-entrance-item mb-3 flex items-center gap-2 rounded-full bg-white px-3 py-2 text-xs leading-snug font-semibold text-brand-neutral-900"
                                    >
                                        <Search class="size-3.5 shrink-0" />
                                        {{ activeHeroDemo.search }}
                                    </div>
                                    <div class="space-y-3">
                                        <button
                                            v-for="tool in tools.slice(0, 3)"
                                            :key="tool.title"
                                            type="button"
                                            :aria-pressed="
                                                activeHeroDemo.tabTitle ===
                                                tool.title
                                            "
                                            :class="[
                                                'w-full rounded-2xl border p-3 text-left transition focus:ring-2 focus:ring-white/70 focus:outline-none',
                                                'hero-demo-entrance-item',
                                                activeHeroDemo.tabTitle ===
                                                tool.title
                                                    ? 'border-white/25 bg-white/16 shadow-[inset_0_0_0_1px_rgba(255,255,255,.12)]'
                                                    : 'border-white/10 bg-white/8 hover:bg-white/12',
                                            ]"
                                            @click="
                                                activeHeroTool = tools
                                                    .slice(0, 3)
                                                    .findIndex(
                                                        (item) =>
                                                            item.title ===
                                                            tool.title,
                                                    )
                                            "
                                        >
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <span
                                                    :class="[
                                                        'grid size-9 place-items-center rounded-xl text-brand-neutral-900',
                                                        tool.tint,
                                                    ]"
                                                >
                                                    <component
                                                        :is="tool.icon"
                                                        class="size-4"
                                                    />
                                                </span>
                                                <div>
                                                    <div
                                                        class="text-xs font-bold"
                                                    >
                                                        {{ tool.title }}
                                                    </div>
                                                    <div
                                                        class="text-[0.7rem] leading-snug text-white/55"
                                                    >
                                                        {{ tool.items[0] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                </aside>

                                <div class="space-y-3 xl:space-y-4">
                                    <template
                                        v-for="(
                                            demo, demoIndex
                                        ) in heroToolDemos"
                                        :key="demo.tabTitle"
                                    >
                                        <div
                                            :aria-hidden="
                                                demoIndex !== activeHeroTool
                                            "
                                            :class="[
                                                demoIndex === activeHeroTool
                                                    ? 'grid'
                                                    : 'hidden',
                                                'hero-demo-entrance-group gap-2 sm:grid-cols-2 xl:gap-3',
                                            ]"
                                        >
                                            <a
                                                v-for="card in demo.cards"
                                                :key="card.title"
                                                :href="card.url"
                                                target="_blank"
                                                rel="noreferrer"
                                                :tabindex="
                                                    demoIndex ===
                                                    activeHeroTool
                                                        ? undefined
                                                        : -1
                                                "
                                                :style="
                                                    card.mediaType ===
                                                        'image' && card.url
                                                        ? {
                                                              backgroundImage: `url('${card.url}'), linear-gradient(135deg, #92adeb, #47d9c9 58%, #2e1401)`,
                                                          }
                                                        : undefined
                                                "
                                                class="hero-demo-entrance-item group relative aspect-4/5 overflow-hidden rounded-3xl bg-[linear-gradient(135deg,#92adeb,#47d9c9)] bg-cover bg-center shadow-[inset_0_0_0_1px_rgba(255,255,255,.08)] transition hover:-translate-y-0.5 focus:ring-2 focus:ring-white/70 focus:outline-none"
                                            >
                                                <video
                                                    v-if="
                                                        card.mediaType ===
                                                            'video' && card.url
                                                    "
                                                    :ref="setHeroDemoVideoRef"
                                                    class="absolute inset-0 size-full object-cover"
                                                    :data-hero-tab-index="
                                                        demoIndex
                                                    "
                                                    :src="card.url"
                                                    autoplay
                                                    muted
                                                    loop
                                                    playsinline
                                                    preload="auto"
                                                />
                                                <div
                                                    class="absolute inset-0 bg-linear-to-t from-brand-neutral-900/85 via-brand-neutral-900/25 to-transparent transition group-hover:from-brand-neutral-900/70"
                                                />
                                                <div
                                                    class="absolute inset-x-0 top-0 z-10 flex items-center justify-between gap-1.5 p-3 xl:p-4"
                                                >
                                                    <div
                                                        class="inline-flex max-w-full overflow-hidden rounded-full bg-white px-2 py-1 text-[0.65rem] leading-tight font-bold text-ellipsis whitespace-nowrap text-brand-neutral-900 xl:max-w-[calc(100%-2.5rem)] xl:px-2.5 xl:text-[0.7rem]"
                                                    >
                                                        <span class="xl:hidden">
                                                            {{
                                                                card.shortLabel ??
                                                                card.label
                                                            }}
                                                        </span>
                                                        <span
                                                            class="hidden xl:inline"
                                                        >
                                                            {{ card.label }}
                                                        </span>
                                                    </div>
                                                    <span
                                                        class="hidden size-8 shrink-0 place-items-center rounded-full bg-white/20 text-white backdrop-blur xl:grid"
                                                    >
                                                        <Play
                                                            v-if="
                                                                card.mediaType ===
                                                                'video'
                                                            "
                                                            class="size-3.5 fill-current xl:size-4"
                                                        />
                                                        <component
                                                            :is="Image"
                                                            v-else
                                                            class="size-3.5 xl:size-4"
                                                        />
                                                    </span>
                                                </div>
                                                <div
                                                    class="absolute inset-x-0 bottom-0 z-10 p-3 xl:p-4"
                                                >
                                                    <div
                                                        class="max-w-44 text-lg leading-tight font-extrabold sm:text-xl xl:text-2xl"
                                                    >
                                                        {{ card.title }}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </template>

                                    <div
                                        class="hero-demo-entrance-group rounded-3xl border border-white/10 bg-white p-3 text-brand-neutral-900 xl:p-4"
                                    >
                                        <div
                                            class="mb-3 flex items-center justify-between"
                                        >
                                            <div
                                                class="text-sm font-extrabold xl:text-base"
                                            >
                                                Generation queue
                                            </div>
                                            <div
                                                class="text-[0.65rem] font-semibold text-brand-neutral-600 xl:text-xs"
                                            >
                                                {{ activeHeroDemo.running }}
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <div
                                                v-for="bar in activeHeroDemo.bars"
                                                :key="bar"
                                                :class="[
                                                    'h-2.5 rounded-full transition-all xl:h-3',
                                                    bar,
                                                ]"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="tools" ref="toolsSection" class="py-20">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div
                    class="mb-10 flex flex-col justify-between gap-5 md:flex-row md:items-end"
                >
                    <div
                        class="transition-all duration-700 ease-out"
                        :class="
                            toolsSectionVisible
                                ? 'translate-y-0 opacity-100'
                                : 'translate-y-8 opacity-0'
                        "
                    >
                        <p
                            class="text-sm font-bold text-brand-blue-600 uppercase"
                        >
                            Marketplace
                        </p>
                        <h2
                            class="mt-3 max-w-2xl text-4xl leading-tight font-extrabold"
                        >
                            Everything a creator reaches for after the idea
                            hits.
                        </h2>
                    </div>
                    <p
                        class="max-w-md text-base leading-7 text-brand-neutral-600 transition-all delay-150 duration-700 ease-out"
                        :class="
                            toolsSectionVisible
                                ? 'translate-y-0 opacity-100'
                                : 'translate-y-8 opacity-0'
                        "
                    >
                        Sell the breadth quickly, then let creators recognize
                        their exact workflow in the category cards.
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div
                        v-for="(tool, index) in tools"
                        :key="tool.title"
                        class="transition-all duration-700 ease-out"
                        :class="
                            toolsSectionVisible
                                ? 'translate-y-0 opacity-100'
                                : 'translate-y-10 opacity-0'
                        "
                        :style="{
                            transitionDelay: toolsSectionVisible
                                ? `${220 + index * 90}ms`
                                : '0ms',
                        }"
                    >
                        <article
                            class="h-full rounded-3xl border border-brand-neutral-900 bg-white p-5 shadow-[4px_4px_0_0_#2e1401] transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-[6px_6px_0_0_#2e1401]"
                        >
                            <span
                                :class="[
                                    'mb-6 grid size-12 place-items-center rounded-2xl border border-brand-neutral-900',
                                    tool.tint,
                                ]"
                            >
                                <component :is="tool.icon" class="size-6" />
                            </span>
                            <h3 class="text-xl font-extrabold">
                                {{ tool.title }}
                            </h3>
                            <p
                                class="mt-3 min-h-24 text-sm leading-6 text-brand-neutral-600"
                            >
                                {{ tool.description }}
                            </p>
                            <div class="mt-5 flex flex-wrap gap-2">
                                <span
                                    v-for="item in tool.items"
                                    :key="item"
                                    class="rounded-full bg-brand-neutral-100 px-3 py-1 text-xs font-bold text-brand-neutral-600"
                                >
                                    {{ item }}
                                </span>
                            </div>
                        </article>
                    </div>
                </div>

                <div class="mt-16">
                    <div class="mb-8 max-w-3xl">
                        <p
                            class="text-sm font-bold text-brand-pink-700 uppercase"
                        >
                            What you can create
                        </p>
                        <h2 class="mt-3 text-4xl leading-tight font-extrabold">
                            Show the generated images and videos like the
                            product.
                        </h2>
                        <p
                            class="mt-4 max-w-2xl text-base leading-7 text-brand-neutral-600"
                        >
                            The grid leads with actual media, then uses compact
                            labels so creators can scan the asset types without
                            covering the work.
                        </p>
                    </div>

                    <div
                        class="grid grid-flow-dense auto-rows-[280px] gap-4 sm:auto-rows-[340px] lg:auto-rows-[170px] lg:grid-cols-12"
                    >
                        <article
                            v-for="item in bentoItems"
                            :key="item.title"
                            :style="
                                item.imageUrl
                                    ? {
                                          backgroundImage: `url('${item.imageUrl}')`,
                                      }
                                    : undefined
                            "
                            :class="[
                                'group relative overflow-hidden rounded-[1.5rem] border border-brand-neutral-900 shadow-[3px_3px_0_0_#2e1401] transition hover:-translate-y-1 hover:shadow-[5px_5px_0_0_#2e1401]',
                                item.imageUrl || item.videoUrl
                                    ? 'flex items-end bg-cover bg-center text-white'
                                    : 'p-5',
                                item.accent,
                                item.layout,
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
                                v-if="item.imageUrl || item.videoUrl"
                                class="absolute inset-0 bg-gradient-to-t from-brand-neutral-900/88 via-brand-neutral-900/28 to-transparent transition group-hover:from-brand-neutral-900/76"
                            />

                            <div
                                v-if="item.imageUrl || item.videoUrl"
                                class="absolute top-4 right-4 z-10 grid size-10 place-items-center rounded-full border border-white/20 bg-white/18 text-white backdrop-blur"
                            >
                                <Play
                                    v-if="item.videoUrl"
                                    class="size-4 fill-current"
                                />
                                <component :is="Image" v-else class="size-4" />
                            </div>

                            <div
                                v-if="
                                    item.visual !== 'brand' ||
                                    item.imageUrl ||
                                    item.videoUrl
                                "
                                :class="[
                                    'relative z-10',
                                    item.imageUrl || item.videoUrl
                                        ? 'w-full p-5 sm:p-6'
                                        : 'max-w-[15rem]',
                                ]"
                            >
                                <span
                                    :class="[
                                        'mb-3 inline-flex items-center gap-2 rounded-full border px-3 py-1 text-[0.7rem] leading-none font-bold',
                                        item.imageUrl || item.videoUrl
                                            ? 'border-white/30 bg-white/15 text-white backdrop-blur'
                                            : 'border-brand-neutral-900 bg-white/85 text-brand-neutral-600',
                                    ]"
                                >
                                    <component
                                        :is="item.icon"
                                        class="size-3.5"
                                    />
                                    {{ item.eyebrow }}
                                </span>
                                <h3
                                    :class="[
                                        'leading-tight font-extrabold',
                                        item.imageUrl || item.videoUrl
                                            ? 'max-w-[17rem] text-3xl'
                                            : 'text-2xl',
                                    ]"
                                >
                                    {{ item.title }}
                                </h3>
                                <p
                                    :class="[
                                        'mt-3 hidden max-w-[22rem] text-sm leading-6 sm:block',
                                        item.imageUrl || item.videoUrl
                                            ? 'text-white/80'
                                            : item.accent.includes('text-white')
                                              ? 'text-white/75'
                                              : 'text-brand-neutral-600',
                                    ]"
                                >
                                    {{ item.description }}
                                </p>
                            </div>

                            <div
                                v-if="!item.imageUrl && !item.videoUrl"
                                class="absolute inset-0"
                            >
                                <div
                                    v-if="item.visual === 'rings'"
                                    class="absolute top-8 right-[-2rem] hidden h-32 w-[58%] items-center justify-around sm:flex"
                                >
                                    <div
                                        v-for="size in [
                                            'size-32',
                                            'size-24',
                                            'size-18',
                                            'size-14',
                                        ]"
                                        :key="size"
                                        :class="[
                                            'rounded-full border-4 border-brand-neutral-900/25 bg-white/35 shadow-[0_0_0_6px_rgba(255,255,255,.6)]',
                                            size,
                                        ]"
                                    />
                                    <span
                                        class="absolute top-1/2 left-0 h-1 w-full bg-brand-neutral-900/40"
                                    />
                                </div>

                                <div
                                    v-else-if="item.visual === 'motion'"
                                    class="absolute inset-x-0 bottom-8 flex justify-center"
                                >
                                    <span
                                        class="absolute top-8 right-8 rounded-full border border-brand-neutral-900 bg-white px-3 py-1 text-xs font-bold"
                                        >Sign-off</span
                                    >
                                    <div
                                        class="relative mt-8 text-6xl font-extrabold tracking-normal text-white sm:text-7xl"
                                    >
                                        Motion
                                        <span
                                            class="absolute top-3 -left-5 size-3 rounded-full bg-brand-neutral-900"
                                        />
                                        <span
                                            class="absolute top-3 -right-4 size-3 rounded-full bg-brand-neutral-900"
                                        />
                                        <span
                                            class="absolute -top-5 left-1/2 size-7 rounded-full border border-brand-neutral-900 bg-brand-blue-400"
                                        />
                                    </div>
                                    <div
                                        class="absolute bottom-8 left-1/2 h-7 w-48 -translate-x-1/2 rounded-full border border-brand-neutral-900 bg-brand-neutral-900"
                                    />
                                </div>

                                <div
                                    v-else-if="item.visual === 'icons'"
                                    class="absolute inset-x-8 bottom-8 grid grid-cols-3 gap-5"
                                >
                                    <div
                                        v-for="tool in tools.slice(0, 4)"
                                        :key="tool.title"
                                        class="grid gap-2 text-center text-[0.65rem] font-bold"
                                    >
                                        <span
                                            class="mx-auto grid size-11 place-items-center rounded-xl bg-brand-neutral-900 text-white"
                                        >
                                            <component
                                                :is="tool.icon"
                                                class="size-5"
                                            />
                                        </span>
                                        {{
                                            tool.title.split(' ')[1] ||
                                            tool.title
                                        }}
                                    </div>
                                </div>

                                <div
                                    v-else-if="item.visual === 'studio'"
                                    class="absolute inset-x-8 bottom-12 h-32 rounded-xl border border-brand-neutral-900 bg-brand-teal-400"
                                >
                                    <div
                                        class="absolute bottom-3 -left-8 size-24 rounded-xl border border-brand-neutral-900 bg-brand-pink-400"
                                    />
                                    <div
                                        class="absolute -right-8 bottom-4 size-24 rounded-xl border border-brand-neutral-900 bg-brand-yellow-500"
                                    />
                                    <div
                                        class="absolute -top-8 right-8 size-24 rounded-full border border-brand-neutral-900 bg-brand-teal-400"
                                    />
                                    <div
                                        class="absolute top-6 -left-7 h-24 w-5 rounded bg-brand-neutral-900"
                                    />
                                </div>

                                <div
                                    v-else-if="item.visual === 'brand'"
                                    class="absolute inset-0"
                                >
                                    <div
                                        class="absolute -top-20 right-28 size-36 rounded-full border border-brand-neutral-900 bg-brand-blue-400"
                                    />
                                    <div
                                        class="absolute -bottom-20 left-24 size-40 rounded-full border border-brand-neutral-900 bg-brand-teal-400"
                                    />
                                    <div
                                        class="absolute -right-8 -bottom-10 size-28 rounded-full border border-brand-neutral-900 bg-brand-yellow-500"
                                    />
                                    <div
                                        class="absolute inset-0 flex items-center justify-center"
                                    >
                                        <div class="flex items-center gap-4">
                                            <span
                                                class="grid size-16 place-items-center rounded-2xl bg-brand-neutral-900 text-brand-yellow-500"
                                            >
                                                <Sparkles class="size-8" />
                                            </span>
                                            <span
                                                class="text-5xl font-extrabold text-brand-neutral-900 sm:text-6xl"
                                                >PromptEdit</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-else-if="item.visual === 'logos'"
                                    class="absolute inset-0 flex items-center justify-center gap-8"
                                >
                                    <span
                                        class="grid size-20 place-items-center rounded-3xl bg-brand-yellow-500 text-brand-neutral-900 shadow-[3px_3px_0_0_#2e1401]"
                                    >
                                        <Sparkles class="size-9" />
                                    </span>
                                    <span
                                        class="grid size-20 place-items-center rounded-3xl bg-brand-blue-400 text-brand-neutral-900 shadow-[3px_3px_0_0_#2e1401]"
                                    >
                                        <Zap class="size-9" />
                                    </span>
                                </div>

                                <div
                                    v-else-if="item.visual === 'cards'"
                                    class="absolute inset-x-8 bottom-10 flex items-end gap-3"
                                >
                                    <div
                                        v-for="offset in [
                                            'translate-y-2',
                                            '-translate-y-1',
                                            'translate-y-4',
                                        ]"
                                        :key="offset"
                                        :class="[
                                            'h-24 flex-1 rounded-xl border border-brand-neutral-900 bg-white p-2 shadow-[2px_2px_0_0_#2e1401]',
                                            offset,
                                        ]"
                                    >
                                        <span
                                            class="block h-2 w-10 rounded-full bg-brand-pink-400"
                                        />
                                        <span
                                            class="mt-4 block h-8 rounded bg-brand-neutral-100"
                                        />
                                        <span
                                            class="mt-2 block h-2 rounded-full bg-brand-neutral-900"
                                        />
                                    </div>
                                </div>

                                <div
                                    v-else-if="item.visual === 'wave'"
                                    class="absolute inset-x-8 bottom-9 flex h-32 items-end gap-2"
                                >
                                    <span
                                        v-for="height in [
                                            'h-10',
                                            'h-24',
                                            'h-16',
                                            'h-32',
                                            'h-20',
                                            'h-28',
                                            'h-12',
                                            'h-24',
                                        ]"
                                        :key="height"
                                        :class="[
                                            'w-full rounded-full border border-brand-neutral-900 bg-brand-pink-400',
                                            height,
                                        ]"
                                    />
                                </div>

                                <div
                                    v-else-if="item.visual === 'mockup'"
                                    class="absolute inset-0 flex items-center justify-center"
                                >
                                    <div
                                        class="mt-16 h-28 w-44 rounded-t-xl rounded-b-[2rem] bg-brand-neutral-900 shadow-[4px_4px_0_0_#2e1401]"
                                    >
                                        <span
                                            class="mx-auto -mt-8 block h-14 w-28 rounded-t-full border-[14px] border-b-0 border-brand-neutral-900"
                                        />
                                        <span
                                            class="mt-8 ml-16 block w-fit rotate-[-8deg] rounded bg-brand-pink-400 px-2 py-1 text-xs font-bold"
                                            >Prompt</span
                                        >
                                    </div>
                                </div>

                                <div
                                    v-else
                                    class="absolute right-5 bottom-5 flex h-[42%] max-h-32 min-h-24 justify-end gap-2 sm:right-6 sm:bottom-6 md:right-8 md:bottom-8 lg:inset-x-8 lg:bottom-8 lg:grid lg:h-auto lg:max-h-none lg:min-h-0 lg:grid-cols-4"
                                >
                                    <div
                                        v-for="tone in [
                                            'bg-white',
                                            'bg-brand-yellow-500',
                                            'bg-brand-pink-400',
                                            'bg-brand-blue-400',
                                        ]"
                                        :key="tone"
                                        :class="[
                                            'aspect-3/5 h-full rounded-lg border border-white/40 p-2 shadow-[2px_2px_0_0_rgba(255,255,255,.35)] lg:h-auto',
                                            tone,
                                        ]"
                                    >
                                        <span
                                            class="mt-8 block h-3 rounded-full bg-brand-neutral-900 lg:mt-12"
                                        />
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <CreatorCarousel :creators="featuredCreators" />

        <PinnedStoryPanels />

        <section class="border-b border-brand-neutral-900/10 bg-white py-14">
            <div
                class="mx-auto grid max-w-7xl gap-8 px-5 sm:px-8 lg:grid-cols-[0.8fr_1.2fr]"
            >
                <div>
                    <p class="text-sm font-bold text-brand-pink-700 uppercase">
                        The problem
                    </p>
                    <h2 class="mt-3 text-4xl leading-tight font-extrabold">
                        Creators should not need five subscriptions to make one
                        video.
                    </h2>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div
                        v-for="item in [
                            'Model hopping',
                            'Surprise monthly bills',
                            'Separate asset libraries',
                            'Manual export chaos',
                        ]"
                        :key="item"
                        class="rounded-3xl border border-brand-neutral-900 bg-brand-neutral-100 p-5 font-bold shadow-[3px_3px_0_0_#2e1401]"
                    >
                        {{ item }}
                    </div>
                </div>
            </div>
        </section>

        <CreatorWorkflowMotion />

        <section
            id="pricing"
            ref="pricingSection"
            class="border-b border-brand-neutral-900/10 bg-white py-20"
        >
            <div
                class="mx-auto grid max-w-7xl gap-8 px-5 sm:px-8 xl:grid-cols-[0.95fr_1.05fr] xl:items-center"
            >
                <div
                    class="transition-all duration-700 ease-out"
                    :class="
                        pricingSectionVisible
                            ? 'translate-y-0 opacity-100'
                            : 'translate-y-8 opacity-0'
                    "
                >
                    <p class="text-sm font-bold text-brand-pink-700 uppercase">
                        Pricing
                    </p>
                    <h2 class="mt-3 text-4xl leading-tight font-extrabold">
                        A credit wallet is easier to say yes to than another
                        monthly bill.
                    </h2>
                    <p
                        class="mt-5 max-w-xl text-base leading-7 text-brand-neutral-600"
                    >
                        Keep the sales argument simple: creators can start with
                        credits, use them across the marketplace, and upgrade
                        only when the template library and faster generation are
                        worth it.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div
                        class="transition-all delay-150 duration-700 ease-out"
                        :class="
                            pricingSectionVisible
                                ? 'translate-y-0 opacity-100'
                                : 'translate-y-10 opacity-0'
                        "
                    >
                        <article
                            class="h-full rounded-4xl border border-brand-neutral-900 bg-brand-neutral-100 p-6 shadow-[5px_5px_0_0_#2e1401] transition-transform duration-300 ease-out xl:-rotate-1 xl:hover:rotate-0"
                        >
                            <Coins class="mb-6 size-8 text-brand-blue-600" />
                            <h3 class="text-2xl font-extrabold">
                                Pay as you go
                            </h3>
                            <p
                                class="mt-2 text-sm font-semibold text-brand-neutral-600"
                            >
                                Buy credits. Use only what you need.
                            </p>
                            <div class="my-8 text-5xl font-extrabold">$98</div>
                            <ul class="space-y-3 text-sm font-medium">
                                <li class="flex gap-2">
                                    <Check class="size-5 text-brand-blue-600" />
                                    $110 worth of AI credits
                                </li>
                                <li class="flex gap-2">
                                    <Check class="size-5 text-brand-blue-600" />
                                    No required subscription
                                </li>
                                <li class="flex gap-2">
                                    <Check class="size-5 text-brand-blue-600" />
                                    Use across supported tools
                                </li>
                            </ul>
                        </article>
                    </div>

                    <div
                        class="transition-all delay-500 duration-700 ease-out"
                        :class="
                            pricingSectionVisible
                                ? 'translate-y-0 opacity-100'
                                : 'translate-y-10 opacity-0'
                        "
                    >
                        <article
                            class="h-full rounded-4xl border border-brand-neutral-900 bg-brand-yellow-500 p-6 shadow-[5px_5px_0_0_#2e1401] transition-transform duration-300 ease-out xl:rotate-1 xl:hover:rotate-0"
                        >
                            <Sparkles class="mb-6 size-8" />
                            <h3 class="text-2xl font-extrabold">
                                Creator membership
                            </h3>
                            <p
                                class="mt-2 text-sm font-semibold text-brand-neutral-600"
                            >
                                Credits plus the asset library.
                            </p>
                            <div class="my-8 text-5xl font-extrabold">
                                $39<span class="text-lg">/mo</span>
                            </div>
                            <ul class="space-y-3 text-sm font-medium">
                                <li class="flex gap-2">
                                    <Check class="size-5" /> $49 credits every
                                    month
                                </li>
                                <li class="flex gap-2">
                                    <Check class="size-5" /> 100,000+ templates
                                    and assets
                                </li>
                                <li class="flex gap-2">
                                    <Check class="size-5" /> Faster creative
                                    iteration
                                </li>
                            </ul>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-brand-neutral-900 py-20 text-white">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div
                    class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between"
                >
                    <div>
                        <p
                            class="text-sm font-bold text-brand-yellow-500 uppercase"
                        >
                            Cleaner by design
                        </p>
                        <h2
                            class="mt-3 max-w-3xl text-4xl leading-tight font-extrabold"
                        >
                            Why this beats subscription sprawl.
                        </h2>
                    </div>
                    <div
                        class="inline-flex w-fit items-center gap-2 rounded-full border border-white bg-white px-4 py-2 text-xs font-extrabold text-brand-neutral-900 shadow-[3px_3px_0_0_var(--color-brand-yellow-500)]"
                    >
                        <Sparkles class="size-4 text-brand-pink-700" />
                        One workspace, fewer tabs
                    </div>
                </div>

                <div
                    class="mt-10 grid gap-6 lg:grid-cols-[1.05fr_0.95fr] lg:items-stretch"
                >
                    <div
                        class="flex h-full flex-col rounded-4xl border border-white bg-brand-yellow-500 p-5 text-brand-neutral-900 shadow-[8px_8px_0_0_var(--color-brand-neutral-900)] sm:p-7 lg:-rotate-1"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-extrabold uppercase">
                                    PromptEdit
                                </p>
                                <h3
                                    class="mt-2 max-w-xl text-3xl leading-tight font-extrabold"
                                >
                                    One creator wallet that keeps the work
                                    moving.
                                </h3>
                            </div>
                            <span
                                class="grid size-14 shrink-0 place-items-center rounded-full border border-brand-neutral-900 bg-white shadow-[3px_3px_0_0_#2e1401]"
                            >
                                <Sparkles class="size-6" />
                            </span>
                        </div>

                        <div
                            class="mt-8 grid flex-1 auto-rows-fr gap-3 sm:grid-cols-2"
                        >
                            <div
                                v-for="item in comparisons"
                                :key="item.promptEdit"
                                class="flex h-full flex-col rounded-3xl border border-brand-neutral-900 bg-white p-4 shadow-[3px_3px_0_0_#2e1401]"
                            >
                                <div
                                    class="flex items-start justify-between gap-2 xl:gap-3"
                                >
                                    <div
                                        class="flex items-center gap-2 xl:gap-3"
                                    >
                                        <span
                                            class="grid size-9 shrink-0 place-items-center rounded-full border border-brand-neutral-900 xl:size-10"
                                            :class="item.accent"
                                        >
                                            <component
                                                :is="item.icon"
                                                class="size-4 xl:size-5"
                                            />
                                        </span>
                                        <p
                                            class="text-[0.65rem] leading-tight font-extrabold uppercase xl:text-xs"
                                        >
                                            {{ item.need }}
                                        </p>
                                    </div>
                                    <span
                                        class="rounded-full border border-brand-neutral-900 bg-brand-neutral-100 px-1.5 py-1 text-[0.52rem] leading-none font-extrabold whitespace-nowrap uppercase xl:px-2.5 xl:text-[0.65rem]"
                                    >
                                        {{ item.badge }}
                                    </span>
                                </div>
                                <p
                                    class="mt-5 text-lg leading-snug font-extrabold"
                                >
                                    {{ item.promptEdit }}
                                </p>
                                <p
                                    class="mt-3 text-sm leading-5 font-semibold text-brand-neutral-600"
                                >
                                    {{ item.detail }}
                                </p>
                                <div
                                    class="mt-auto flex items-center gap-2 pt-5"
                                >
                                    <span
                                        class="h-2 flex-1 rounded-full border border-brand-neutral-900"
                                        :class="item.accent"
                                    ></span>
                                    <span
                                        class="h-2 w-8 rounded-full border border-brand-neutral-900 bg-brand-neutral-900"
                                    ></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex h-full flex-col gap-5 lg:pt-8">
                        <div
                            class="flex items-center justify-between gap-3 rounded-3xl border border-white/20 bg-white/10 px-5 py-4"
                        >
                            <p class="text-sm font-extrabold uppercase">
                                Separate tools
                            </p>
                            <span class="text-sm font-bold text-white/70">
                                The tab pile-up
                            </span>
                        </div>

                        <article
                            v-for="item in comparisons"
                            :key="item.separateTools"
                            class="flex-1 rounded-3xl border border-brand-neutral-900 bg-white p-5 text-brand-neutral-900 shadow-[5px_5px_0_0_var(--color-brand-yellow-500)] transition-transform duration-300 ease-out lg:odd:rotate-1 lg:even:-rotate-1 lg:hover:rotate-0"
                        >
                            <p
                                class="text-brand-neutral-500 text-xs font-extrabold uppercase"
                            >
                                {{ item.need }}
                            </p>
                            <div class="mt-3 flex items-start gap-3">
                                <span
                                    class="mt-1 grid size-8 shrink-0 place-items-center rounded-full border border-brand-neutral-900 bg-brand-neutral-100"
                                >
                                    <PlugZap class="size-4" />
                                </span>
                                <p class="text-xl leading-snug font-extrabold">
                                    {{ item.separateTools }}
                                </p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white py-20">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="grid gap-4 md:grid-cols-3">
                    <figure
                        v-for="testimonial in testimonials"
                        :key="testimonial.name"
                        class="rounded-3xl border border-brand-neutral-900 bg-brand-neutral-100 p-6 shadow-[3px_3px_0_0_#2e1401]"
                    >
                        <blockquote class="text-lg leading-7 font-bold">
                            “{{ testimonial.quote }}”
                        </blockquote>
                        <figcaption class="mt-6 text-sm text-brand-neutral-600">
                            <strong class="block text-brand-neutral-900">{{
                                testimonial.name
                            }}</strong>
                            {{ testimonial.role }}
                        </figcaption>
                    </figure>
                </div>
            </div>
        </section>

        <section id="faq" class="py-20">
            <div
                class="mx-auto grid max-w-7xl gap-8 px-5 sm:px-8 lg:grid-cols-[0.7fr_1.3fr]"
            >
                <div>
                    <p class="text-sm font-bold text-brand-blue-600 uppercase">
                        FAQ
                    </p>
                    <h2 class="mt-3 text-4xl leading-tight font-extrabold">
                        Answer objections before the CTA.
                    </h2>
                </div>
                <div class="space-y-3">
                    <article
                        v-for="(faq, index) in faqs"
                        :key="faq.question"
                        class="overflow-hidden rounded-3xl border border-brand-neutral-900 bg-white shadow-[2px_2px_0_0_#2e1401]"
                    >
                        <button
                            type="button"
                            class="flex w-full cursor-pointer items-center justify-between gap-4 p-5 text-left font-extrabold"
                            :aria-expanded="activeFaqIndex === index"
                            :aria-controls="`faq-answer-${index}`"
                            @click="toggleFaq(index)"
                        >
                            <span>{{ faq.question }}</span>
                            <span
                                class="flex size-7 shrink-0 items-center justify-center rounded-full border border-brand-neutral-900 text-xl leading-none"
                                aria-hidden="true"
                            >
                                {{ activeFaqIndex === index ? '−' : '+' }}
                            </span>
                        </button>
                        <div
                            :id="`faq-answer-${index}`"
                            class="grid transition-[grid-template-rows,opacity] duration-300 ease-out"
                            :class="
                                activeFaqIndex === index
                                    ? 'grid-rows-[1fr] opacity-100'
                                    : 'grid-rows-[0fr] opacity-0'
                            "
                        >
                            <div class="overflow-hidden">
                                <p
                                    class="px-5 pb-5 text-sm leading-6 text-brand-neutral-600"
                                >
                                    {{ faq.answer }}
                                </p>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="px-5 pb-10 sm:px-8">
            <div
                class="mx-auto max-w-7xl rounded-[2rem] border border-brand-neutral-900 bg-brand-pink-400 p-8 text-center shadow-[6px_6px_0_0_#2e1401] sm:p-12"
            >
                <h2
                    class="mx-auto max-w-3xl text-4xl leading-tight font-extrabold"
                >
                    Start with credits. Create with the full marketplace.
                </h2>
                <p
                    class="mx-auto mt-4 max-w-2xl text-base leading-7 font-medium text-brand-neutral-900/75"
                >
                    A better sales page should make the next step obvious,
                    low-friction, and visually connected to the creator
                    workflow.
                </p>
                <a
                    href="https://promptedit.com"
                    class="mt-8 inline-flex min-h-12 items-center justify-center gap-2 rounded-full bg-brand-neutral-900 px-6 py-3 font-bold text-white transition hover:bg-brand-blue-600"
                >
                    Claim the creator offer
                    <ArrowRight class="size-5" />
                </a>
            </div>
        </section>
    </main>
</template>
