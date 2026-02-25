<template>
    <div class="bg-white w-full min-h-[297mm] animate-pulse p-10 shadow-inner">
        <div class="h-8 bg-gray-200 rounded w-1/2 mb-4"></div>
        <div class="h-4 bg-gray-100 rounded w-1/3 mb-8"></div>
        <div class="space-y-3">
            <div class="h-3 bg-gray-100 rounded w-full"></div>
            <div class="h-3 bg-gray-100 rounded w-5/6"></div>
            <div class="h-3 bg-gray-100 rounded w-4/6"></div>
        </div>
        <div class="mt-12 space-y-8">
            <div v-for="i in 3" :key="i" class="space-y-4">
                <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                <div class="h-3 bg-gray-100 rounded w-full"></div>
                <div class="h-3 bg-gray-100 rounded w-5/6"></div>
            </div>
        </div>
    </div>
</template>
