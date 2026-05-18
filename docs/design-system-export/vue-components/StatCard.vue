<script setup lang="ts">
withDefaults(
  defineProps<{
    label: string | number;
    value: string | number;
    accentClass?: string;
    iconClass?: string;
    class?: string;
  }>(),
  {
    accentClass: "bg-brand-blue-400",
    iconClass: "text-brand-neutral-900",
    class: "",
  },
);
</script>

<template>
  <div
    :class="[
      'grid min-h-[120px] w-full overflow-hidden rounded-xl border-brand-neutral-900 text-brand-neutral-900',
      'border-t border-r-2 border-b-2 border-l',
      'grid-cols-[minmax(0,1fr)_98px]',
      $props.class,
    ]"
  >
    <div class="flex min-w-0 flex-col justify-center bg-brand-neutral-0 px-5 py-4">
      <p class="text-preset-4">{{ label }}</p>
      <p class="mt-4 text-[2.5rem] leading-none font-bold tracking-normal">
        {{ value }}
      </p>
    </div>

    <div
      :class="[
        'flex items-center justify-center border-l border-brand-neutral-900',
        accentClass,
      ]"
    >
      <span
        aria-hidden="true"
        :class="[
          'inline-flex size-7 items-center justify-center [&_svg]:size-full',
          iconClass,
        ]"
      >
        <slot name="icon" />
      </span>
    </div>
  </div>
</template>
