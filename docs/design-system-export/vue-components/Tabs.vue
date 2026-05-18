<script setup lang="ts">
import { computed, nextTick, ref, useId } from "vue";
import Button from "./Button.vue";

type TabItem = {
  value: string;
  label: string;
  disabled?: boolean;
  panel?: string;
};

const props = withDefaults(
  defineProps<{
    tabs: TabItem[];
    modelValue?: string;
    defaultValue?: string;
    ariaLabel?: string;
    renderPanels?: boolean;
    class?: string;
    tabClass?: string;
    tabListClass?: string;
    panelClass?: string;
  }>(),
  {
    ariaLabel: "Card view",
    renderPanels: true,
    class: "",
    tabClass: "",
    tabListClass: "",
    panelClass: "",
  },
);

const emit = defineEmits<{
  "update:modelValue": [value: string];
}>();

const componentId = useId();
const enabledTabs = computed(() => props.tabs.filter((tab) => !tab.disabled));
const internalValue = ref(
  props.defaultValue ?? enabledTabs.value[0]?.value ?? props.tabs[0]?.value ?? "",
);
const activeValue = computed(() => props.modelValue ?? internalValue.value);
const activeTab = computed(
  () =>
    props.tabs.find((tab) => tab.value === activeValue.value) ??
    enabledTabs.value[0],
);
function selectTab(nextValue: string) {
  if (props.modelValue === undefined) {
    internalValue.value = nextValue;
  }

  emit("update:modelValue", nextValue);
}

function focusTab(nextIndex: number) {
  const nextTab = props.tabs[nextIndex];

  if (!nextTab || nextTab.disabled) {
    return;
  }

  selectTab(nextTab.value);
  nextTick(() => {
    document.getElementById(`${componentId}-${nextTab.value}-tab`)?.focus();
  });
}

function handleKeyDown(event: KeyboardEvent) {
  const currentIndex = props.tabs.findIndex(
    (tab) => tab.value === activeTab.value?.value,
  );

  if (currentIndex === -1 || enabledTabs.value.length === 0) {
    return;
  }

  const enabledIndexes = props.tabs
    .map((tab, index) => (tab.disabled ? -1 : index))
    .filter((index) => index >= 0);
  const enabledPosition = enabledIndexes.indexOf(currentIndex);

  if (event.key === "ArrowRight" || event.key === "ArrowDown") {
    event.preventDefault();
    focusTab(enabledIndexes[(enabledPosition + 1) % enabledIndexes.length]);
  }

  if (event.key === "ArrowLeft" || event.key === "ArrowUp") {
    event.preventDefault();
    focusTab(
      enabledIndexes[
        (enabledPosition - 1 + enabledIndexes.length) % enabledIndexes.length
      ],
    );
  }

  if (event.key === "Home") {
    event.preventDefault();
    focusTab(enabledIndexes[0]);
  }

  if (event.key === "End") {
    event.preventDefault();
    focusTab(enabledIndexes[enabledIndexes.length - 1]);
  }
}
</script>

<template>
  <div :class="['w-full', props.class]">
    <div
      role="tablist"
      :aria-label="ariaLabel"
      :class="[
        'inline-flex w-full gap-1 rounded-full border border-brand-neutral-900 bg-brand-neutral-0 p-1 shadow-[1px_2px_0_0_var(--color-brand-neutral-900)]',
        tabListClass,
      ]"
      @keydown="handleKeyDown"
    >
      <Button
        v-for="tab in tabs"
        :id="`${componentId}-${tab.value}-tab`"
        :key="tab.value"
        role="tab"
        :aria-controls="renderPanels ? `${componentId}-${tab.value}-panel` : undefined"
        :aria-selected="tab.value === activeTab?.value"
        :class="['h-11 min-w-0 flex-1 whitespace-nowrap px-4 py-3', tabClass].join(' ')"
        :disabled="tab.disabled"
        :tabindex="tab.value === activeTab?.value ? 0 : -1"
        :variant="tab.value === activeTab?.value ? 'base-primary' : 'base-secondary'"
        @click="selectTab(tab.value)"
      >
        {{ tab.label }}
      </Button>
    </div>

    <template v-if="renderPanels">
      <div
        v-for="tab in tabs"
        :id="`${componentId}-${tab.value}-panel`"
        :key="tab.value"
        role="tabpanel"
        :aria-labelledby="`${componentId}-${tab.value}-tab`"
        :hidden="tab.value !== activeTab?.value"
        :tabindex="0"
        :class="[
          'mt-4 text-preset-5-regular text-brand-neutral-900',
          tab.value === activeTab?.value ? '' : 'hidden',
          panelClass,
        ]"
      >
        <slot :name="`panel-${tab.value}`">
          {{ tab.panel }}
        </slot>
      </div>
    </template>
  </div>
</template>
