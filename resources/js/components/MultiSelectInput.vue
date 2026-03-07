<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from "vue";

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: "Select options...",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    name: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["update:modelValue"]);

const wrapper = ref(null);
const searchInput = ref(null);
const isOpen = ref(false);
const search = ref("");
const activeIndex = ref(-1);
const localValue = ref([]);

const syncLocalValue = (value) => {
    if (Array.isArray(value)) {
        localValue.value = [...new Set(value)];
    } else {
        localValue.value = [];
    }
};

watch(
    () => props.modelValue,
    (newValue) => {
        syncLocalValue(newValue);
    },
    { immediate: true }
);

const filteredOptions = computed(() => {
    const term = search.value.trim().toLowerCase();
    if (!term) {
        return props.options;
    }
    return props.options.filter((option) => {
        const label = String(option.label ?? option.value ?? "")
            .toLowerCase()
            .trim();
        return label.includes(term);
    });
});

const selectedLabels = computed(() => {
    const labelMap = new Map(
        props.options.map((option) => [option.value, option.label])
    );
    return localValue.value
        .map((value) => ({ value, label: labelMap.get(value) }))
        .filter((item) => item.label);
});

const selectionSummary = computed(() => {
    if (selectedLabels.value.length === 0) {
        return props.placeholder;
    }
    if (selectedLabels.value.length === 1) {
        return selectedLabels.value[0].label;
    }
    return `${selectedLabels.value.length} items selected`;
});

const openDropdown = async () => {
    if (props.disabled) return;
    isOpen.value = true;
    await nextTick();
    if (searchInput.value) {
        searchInput.value.focus();
    }
};

const closeDropdown = () => {
    isOpen.value = false;
    search.value = "";
    activeIndex.value = -1;
};

const toggleDropdown = () => {
    if (isOpen.value) {
        closeDropdown();
        return;
    }
    openDropdown();
};

const onClickOutside = (event) => {
    if (!wrapper.value) return;
    if (!wrapper.value.contains(event.target)) {
        closeDropdown();
    }
};

const applySelection = (value) => {
    if (value === "" || value === null || value === undefined) {
        localValue.value = [];
        emit("update:modelValue", []);
        return;
    }

    const set = new Set(localValue.value.filter((item) => item !== ""));
    if (set.has(value)) {
        set.delete(value);
    } else {
        set.add(value);
    }
    const updated = Array.from(set);
    localValue.value = updated;
    emit("update:modelValue", updated);
};

const toggleOption = (option) => {
    if (props.disabled || option.disabled) return;
    applySelection(option.value);
};

const removeValue = (value) => {
    const nextValues = localValue.value.filter((item) => item !== value);
    localValue.value = nextValues;
    emit("update:modelValue", nextValues);
};

const handleKeydown = (event) => {
    if (props.disabled) return;

    if (!isOpen.value && (event.key === "Enter" || event.key === "ArrowDown")) {
        event.preventDefault();
        openDropdown();
        return;
    }

    if (!isOpen.value) return;

    if (event.key === "Escape") {
        event.preventDefault();
        closeDropdown();
        return;
    }

    if (event.key === "ArrowDown") {
        event.preventDefault();
        const max = filteredOptions.value.length - 1;
        activeIndex.value = Math.min(activeIndex.value + 1, max);
        return;
    }

    if (event.key === "ArrowUp") {
        event.preventDefault();
        activeIndex.value = Math.max(activeIndex.value - 1, 0);
        return;
    }

    if (event.key === "Enter") {
        event.preventDefault();
        const option = filteredOptions.value[activeIndex.value];
        if (option) {
            toggleOption(option);
        }
        return;
    }

    if (event.key === "Backspace" && search.value === "") {
        if (localValue.value.length > 0) {
            removeValue(localValue.value[localValue.value.length - 1]);
        }
    }
};

watch(search, () => {
    if (filteredOptions.value.length > 0) {
        activeIndex.value = 0;
    } else {
        activeIndex.value = -1;
    }
});

onMounted(() => {
    document.addEventListener("mousedown", onClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener("mousedown", onClickOutside);
});
</script>

<template>
    <div ref="wrapper" class="relative" @keydown="handleKeydown">
        <div datatype="multi-select" class="text-sm rounded-sm border border-gray-300 shadow-sm focus-within:border-purple-500 focus-within:ring-1 focus-within:ring-purple-500 disabled:cursor-not-allowed disabled:bg-gray-200 flex items-center gap-2 px-3 py-2"
            :class="{ 'opacity-60 pointer-events-none': disabled }" role="button" tabindex="0" @click="toggleDropdown">
            <span class="flex-1 min-w-0 truncate"
                :class="selectedLabels.length ? 'text-gray-900' : 'text-gray-400'">
                {{ selectionSummary }}
            </span>
        </div>

        <div v-if="isOpen"
            class="absolute z-50 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-lg transition">
            <div class="p-2 border-b border-gray-200">
                <input ref="searchInput" v-model="search" type="text"
                    class="w-full text-sm rounded-sm border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Search..." />
            </div>

            <ul class="max-h-56 overflow-auto py-1">
                <li v-for="(option, index) in filteredOptions" :key="String(option.value)"
                    class="px-3 py-2 text-sm cursor-pointer flex items-center gap-2 hover:bg-[#1967d2] hover:text-white"
                    :class="{
                        'opacity-50 cursor-not-allowed': option.disabled,
                        'bg-gray-100': index === activeIndex,
                        'bg-[#1967d2] text-white': localValue.includes(option.value),
                    }" @click="toggleOption(option)">
                    <span class="truncate">{{ option.label }}</span>
                </li>
                <li v-if="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-500">
                    No results found.
                </li>
            </ul>
        </div>

        <input type="hidden" :name="name" :value="localValue.join(',')" />
    </div>
</template>