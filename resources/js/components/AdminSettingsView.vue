<template>
  <div class="flex h-screen bg-[#F8F9FA] text-slate-800 antialiased font-sans overflow-hidden">
    <!-- 1. LEFT SIDEBAR WITH INTERACTIVE ACCORDION DROPDOWNS -->
    <aside class="w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between shrink-0 select-none overflow-y-auto">
      <div>
        <!-- Logo (Matching "SETUP LOGO" in reference) -->
        <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-100 shrink-0">
          <div class="w-8 h-8 rounded-lg bg-black text-white flex items-center justify-center font-black text-lg">
            ✱
          </div>
          <div>
            <span class="font-bold tracking-tight text-slate-900 text-sm uppercase block leading-none">SETUP LOGO</span>
            <span class="text-[10px] text-slate-400 font-mono">HERA HR & PAYROLL</span>
          </div>
        </div>

        <!-- Navigation Menu Groups -->
        <div class="px-3 py-4 space-y-4">
          <!-- CORE MENU -->
          <div>
            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">CORE</span>
            <nav class="mt-1 space-y-0.5">
              <button
                type="button"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition"
                :class="activeItem === 'dashboard' ? 'text-slate-900 bg-slate-100 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'"
                @click="activeItem = 'dashboard'"
              >
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                <span>Dashboard</span>
              </button>

              <button
                type="button"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition"
                :class="activeItem === 'setting' ? 'text-slate-900 bg-slate-100 shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'"
                @click="activeItem = 'setting'"
              >
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Setting</span>
              </button>
            </nav>
          </div>

          <!-- ============================================ -->
          <!-- 1. PEOPLE DROPDOWN (Exact 9 items from Image 1) -->
          <!-- ============================================ -->
          <div>
            <button
              type="button"
              class="w-full px-3 py-1.5 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900 rounded-lg hover:bg-slate-50 transition"
              @click="toggleDropdown('people')"
            >
              <div class="flex items-center gap-2">
                <span>PEOPLE</span>
                <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-slate-100 text-slate-500 font-mono">9</span>
              </div>
              <svg
                class="w-3.5 h-3.5 transition-transform duration-200"
                :class="openDropdowns.people ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Content (All 9 items) -->
            <nav v-show="openDropdowns.people" class="mt-1 space-y-0.5 pl-2 border-l border-slate-100 ml-3">
              <button
                v-for="sub in peopleModules"
                :key="sub.id"
                type="button"
                class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs transition text-left"
                :class="activeItem === sub.id ? 'text-slate-900 bg-slate-100 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                @click="activeItem = sub.id"
              >
                <span class="text-sm shrink-0">{{ sub.icon }}</span>
                <span class="truncate">{{ sub.label }}</span>
              </button>
            </nav>
          </div>

          <!-- ================================================== -->
          <!-- 2. TIME & LEAVE DROPDOWN (Exact 15 items from Image 2) -->
          <!-- ================================================== -->
          <div>
            <button
              type="button"
              class="w-full px-3 py-1.5 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900 rounded-lg hover:bg-slate-50 transition"
              @click="toggleDropdown('time_leave')"
            >
              <div class="flex items-center gap-2">
                <span>TIME & LEAVE</span>
                <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-slate-100 text-slate-500 font-mono">15</span>
              </div>
              <svg
                class="w-3.5 h-3.5 transition-transform duration-200"
                :class="openDropdowns.time_leave ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Content (All 15 items) -->
            <nav v-show="openDropdowns.time_leave" class="mt-1 space-y-0.5 pl-2 border-l border-slate-100 ml-3">
              <button
                v-for="sub in timeLeaveModules"
                :key="sub.id"
                type="button"
                class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs transition text-left"
                :class="activeItem === sub.id ? 'text-slate-900 bg-slate-100 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                @click="activeItem = sub.id"
              >
                <span class="text-sm shrink-0">{{ sub.icon }}</span>
                <span class="truncate">{{ sub.label }}</span>
              </button>
            </nav>
          </div>

          <!-- ============================================== -->
          <!-- 3. PAYROLL DROPDOWN (Exact 10 items from Image 3) -->
          <!-- ============================================== -->
          <div>
            <button
              type="button"
              class="w-full px-3 py-1.5 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900 rounded-lg hover:bg-slate-50 transition"
              @click="toggleDropdown('payroll')"
            >
              <div class="flex items-center gap-2">
                <span>PAYROLL</span>
                <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-slate-100 text-slate-500 font-mono">10</span>
              </div>
              <svg
                class="w-3.5 h-3.5 transition-transform duration-200"
                :class="openDropdowns.payroll ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Content (All 10 items) -->
            <nav v-show="openDropdowns.payroll" class="mt-1 space-y-0.5 pl-2 border-l border-slate-100 ml-3">
              <button
                v-for="sub in payrollModules"
                :key="sub.id"
                type="button"
                class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs transition text-left"
                :class="activeItem === sub.id ? 'text-slate-900 bg-slate-100 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                @click="activeItem = sub.id"
              >
                <span class="text-sm shrink-0">{{ sub.icon }}</span>
                <span class="truncate">{{ sub.label }}</span>
              </button>
            </nav>
          </div>

          <!-- ================================================== -->
          <!-- 4. RECRUITMENT DROPDOWN (Exact 31 items from Images 4 & 5) -->
          <!-- ================================================== -->
          <div>
            <button
              type="button"
              class="w-full px-3 py-1.5 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900 rounded-lg hover:bg-slate-50 transition"
              @click="toggleDropdown('recruitment')"
            >
              <div class="flex items-center gap-2">
                <span>RECRUITMENT</span>
                <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-slate-100 text-slate-500 font-mono">31</span>
              </div>
              <svg
                class="w-3.5 h-3.5 transition-transform duration-200"
                :class="openDropdowns.recruitment ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Content (All 31 items from user's images in exact order) -->
            <nav v-show="openDropdowns.recruitment" class="mt-1 space-y-0.5 pl-2 border-l border-slate-100 ml-3">
              <button
                v-for="sub in recruitmentModules"
                :key="sub.id"
                type="button"
                class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs transition text-left"
                :class="activeItem === sub.id ? 'text-slate-900 bg-slate-100 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                @click="activeItem = sub.id"
              >
                <span class="text-sm shrink-0">{{ sub.icon }}</span>
                <span class="truncate">{{ sub.label }}</span>
              </button>
            </nav>
          </div>
        </div>
      </div>

      <!-- Bottom Help Menu -->
      <div class="p-4 border-t border-slate-100 shrink-0">
        <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">HELP</span>
        <div class="mt-1 space-y-0.5">
          <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition">
            <span>📖</span>
            <span>User Guide</span>
          </a>
          <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition">
            <span>💬</span>
            <span>Support</span>
          </a>
        </div>
      </div>
    </aside>

    <!-- 2. MAIN WORKSPACE -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- TOP BAR (Exact layout from user's original reference) -->
      <header class="h-16 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between shrink-0">
        <!-- Search Input matching reference image -->
        <div class="relative w-80">
          <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input
            type="text"
            placeholder="Find percipient..."
            class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-black"
          />
        </div>

        <!-- Right Side: Notifications & Profile Pill (Matching reference image) -->
        <div class="flex items-center gap-4">
          <button class="w-9 h-9 rounded-xl border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          </button>

          <!-- User Profile info matching reference image: Sarah Johnson -->
          <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
            <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-800 flex items-center justify-center font-bold text-xs uppercase overflow-hidden border border-slate-200">
              <span v-if="!currentUser.avatar">SJ</span>
              <img v-else :src="currentUser.avatar" alt="Avatar" class="w-full h-full object-cover" />
            </div>
            <div class="text-left hidden sm:block">
              <div class="flex items-center gap-1.5">
                <span class="text-xs font-bold text-slate-900 block leading-tight">{{ currentUser.name }}</span>
                <span class="text-slate-400 text-xs">⌵</span>
              </div>
              <span class="text-[11px] text-slate-400 block font-mono">{{ currentUser.email }}</span>
            </div>

            <form method="POST" action="/logout" class="inline ml-2">
              <input type="hidden" name="_token" :value="csrfToken" />
              <button type="submit" class="px-2.5 py-1 text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition font-medium">
                Log out
              </button>
            </form>
          </div>
        </div>
      </header>

      <!-- PAGE CONTENT (Scrollable) -->
      <main class="flex-1 overflow-y-auto p-8 space-y-6">

        <!-- ======================================================== -->
        <!-- VIEW 1: SETTING (Exact layout from user's original reference) -->
        <!-- ======================================================== -->
        <div v-if="activeItem === 'setting'" class="space-y-6">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Setting</h1>
              <span class="text-xs font-mono text-slate-400">NDIS 435678965</span>
            </div>

            <div class="flex items-center gap-3">
              <button
                type="button"
                class="px-4 py-2 border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 font-semibold rounded-xl text-xs flex items-center gap-2 shadow-sm transition"
                @click="openAddUserModal"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Upload file</span>
              </button>
              <button
                type="button"
                class="px-4 py-2 bg-black hover:bg-slate-800 text-white font-semibold rounded-xl text-xs flex items-center gap-2 shadow-sm transition"
                @click="openAddUserModal"
              >
                <span>+ Participants</span>
              </button>
            </div>
          </div>

          <!-- Sub-Navigation Pills (Matching Reference Image) -->
          <div class="flex items-center gap-2 overflow-x-auto border-b border-slate-200/80 pb-3">
            <button
              type="button"
              class="px-3.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition"
              :class="activeSubTab === 'organization' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-800'"
              @click="activeSubTab === 'organization'"
            >
              <span>📁</span>
              <span>Organization</span>
            </button>

            <button
              type="button"
              class="px-3.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition"
              :class="activeSubTab === 'users' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-800'"
              @click="activeSubTab === 'users'"
            >
              <span>👤</span>
              <span>User & Permissions</span>
            </button>

            <button
              type="button"
              class="px-3.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition"
              :class="activeSubTab === 'roles' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-800'"
              @click="activeSubTab === 'roles'"
            >
              <span>🔐</span>
              <span>Roles & Role Access</span>
            </button>

            <button
              type="button"
              class="px-3.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition"
              :class="activeSubTab === 'compliance' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-800'"
              @click="activeSubTab === 'compliance'"
            >
              <span>⚖️</span>
              <span>Compliance</span>
            </button>
          </div>

          <!-- TAB: USER DETAILS TABLE (EXACT REFERENCE DESIGN) -->
          <div v-if="activeSubTab === 'users'" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <!-- Table Controls Header -->
            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100">
              <div class="flex items-center gap-3">
                <h2 class="text-base font-bold text-slate-900">User Details</h2>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 font-mono">
                  {{ users.length }}
                </span>
              </div>

              <div class="flex items-center gap-3">
                <div class="relative w-64">
                  <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search goals"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-1.5 text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-black"
                  />
                  <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <button class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 text-xs">
                  ⚡
                </button>

                <button
                  type="button"
                  class="px-4 py-2 bg-black hover:bg-slate-800 text-white font-semibold rounded-xl text-xs flex items-center gap-2 shadow-sm transition"
                  @click="openAddUserModal"
                >
                  <span>+ Add User</span>
                </button>
              </div>
            </div>

            <!-- The Data Table -->
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50/75 text-slate-500 font-semibold border-b border-slate-100 uppercase tracking-wider text-[11px]">
                  <tr>
                    <th scope="col" class="w-10 px-4 py-3.5">
                      <input type="checkbox" class="rounded border-slate-300 text-black focus:ring-black" />
                    </th>
                    <th scope="col" class="px-4 py-3.5">User Name</th>
                    <th scope="col" class="px-4 py-3.5">Email Address</th>
                    <th scope="col" class="px-4 py-3.5">User Role</th>
                    <th scope="col" class="px-4 py-3.5">Status</th>
                    <th scope="col" class="px-4 py-3.5">Add Date</th>
                    <th scope="col" class="px-4 py-3.5">Last Active</th>
                    <th scope="col" class="px-4 py-3.5 text-center">Access</th>
                    <th scope="col" class="px-4 py-3.5 text-center">Action</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-slate-50/60 transition-colors">
                    <td class="px-4 py-3.5">
                      <input type="checkbox" class="rounded border-slate-300 text-black focus:ring-black" />
                    </td>
                    <td class="px-4 py-3.5">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs uppercase border border-slate-200 shrink-0">
                          {{ user.name.split(' ').map(n => n[0]).join('').substring(0, 2) }}
                        </div>
                        <div>
                          <span class="font-bold text-slate-900 block leading-tight">{{ user.name }}</span>
                          <span class="text-[11px] text-slate-400 block">Description Text</span>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3.5 text-slate-600 font-mono">{{ user.email }}</td>
                    <td class="px-4 py-3.5 font-medium text-slate-800">{{ formatRoleName(user.role) }}</td>
                    <td class="px-4 py-3.5">
                      <span
                        class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold inline-block border"
                        :class="user.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-600 border-rose-200'"
                      >
                        {{ user.status }}
                      </span>
                    </td>
                    <td class="px-4 py-3.5 text-slate-500">{{ user.created_at || '—' }}</td>
                    <td class="px-4 py-3.5 text-slate-500">{{ user.last_login_at }}</td>
                    
                    <!-- iOS-style Access Toggle Switch -->
                    <td class="px-4 py-3.5 text-center">
                      <button
                        type="button"
                        :disabled="user.role === 'super-admin'"
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none disabled:opacity-40 disabled:cursor-not-allowed"
                        :class="user.is_active ? 'bg-emerald-600' : 'bg-slate-300'"
                        @click="toggleUserAccess(user)"
                      >
                        <span
                          class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out"
                          :class="user.is_active ? 'translate-x-4' : 'translate-x-0'"
                        />
                      </button>
                    </td>

                    <!-- 3-Dots Action Dropdown Menu -->
                    <td class="px-4 py-3.5 text-center relative">
                      <button
                        type="button"
                        class="w-7 h-7 inline-flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-900 transition"
                        @click="activeActionDropdown = activeActionDropdown === user.id ? null : user.id"
                      >
                        ⋮
                      </button>

                      <div
                        v-if="activeActionDropdown === user.id"
                        class="absolute right-4 top-10 w-40 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-20 text-left text-xs space-y-0.5"
                      >
                        <button
                          type="button"
                          class="w-full px-3 py-1.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2"
                          @click="viewProfile(user)"
                        >
                          <span>👤</span>
                          <span>View Profile</span>
                        </button>
                        <button
                          type="button"
                          class="w-full px-3 py-1.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2"
                          @click="editUser(user)"
                        >
                          <span>✏️</span>
                          <span>Edit Details</span>
                        </button>
                        <button
                          v-if="user.role !== 'super-admin'"
                          type="button"
                          class="w-full px-3 py-1.5 hover:bg-slate-50 text-rose-600 flex items-center gap-2"
                          @click="deleteUser(user)"
                        >
                          <span>🗑️</span>
                          <span>Delete User</span>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination Footer -->
            <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
              <span>Showing 1-{{ filteredUsers.length }} from {{ users.length }}</span>
              <div class="flex items-center gap-2">
                <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold">
                  Previous
                </button>
                <button class="px-3 py-1.5 rounded-lg bg-black text-white font-semibold">
                  Next
                </button>
              </div>
            </div>
          </div>

          <!-- TAB: ROLES & ROLE ACCESS MATRIX -->
          <div v-if="activeSubTab === 'roles'" class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
              <h2 class="text-base font-bold text-slate-900 mb-1">Platform Roles & Permission Matrix</h2>
              <p class="text-xs text-slate-500 mb-6">
                Configure fine-grained module access and authorization boundaries across platform roles.
              </p>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                  v-for="role in roles"
                  :key="role.id"
                  class="border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition"
                >
                  <div class="flex items-start justify-between">
                    <div>
                      <h3 class="font-bold text-slate-900 text-sm">{{ role.name }}</h3>
                      <span class="text-xs font-mono text-slate-400 block mt-0.5">Role Code: {{ role.id }}</span>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                      {{ role.is_system ? 'System Role' : 'Custom' }}
                    </span>
                  </div>

                  <p class="text-xs text-slate-600 mt-2 mb-4 leading-relaxed">
                    {{ role.description }}
                  </p>

                  <div class="border-t border-slate-100 pt-3 space-y-1.5">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Assigned Permissions</span>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                      <label
                        v-for="(granted, perm) in role.permissions"
                        :key="perm"
                        class="flex items-center gap-2 text-slate-700 cursor-pointer"
                      >
                        <input
                          type="checkbox"
                          :checked="granted"
                          @change="togglePermission(role, perm)"
                          class="rounded border-slate-300 text-black focus:ring-black"
                        />
                        <span class="font-mono text-[11px]">{{ perm }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB: ORGANIZATION -->
          <div v-if="activeSubTab === 'organization'" class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900">Registered Campuses & Tenants</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-2">
              <div v-for="t in tenants" :key="t.id" class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                <span class="text-xs font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full inline-block">{{ t.code }}</span>
                <h3 class="font-bold text-slate-900 text-sm">{{ t.name }}</h3>
                <p class="text-xs text-slate-500">Status: Active School Organization</p>
              </div>
            </div>
          </div>

          <!-- TAB: COMPLIANCE -->
          <div v-if="activeSubTab === 'compliance'" class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900">Philippine Statutory Compliance Matrix</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 text-xs">
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                <span class="font-bold text-slate-900 block text-sm">BIR Withholding Tax</span>
                <p class="text-slate-600">RA 10963 (TRAIN Law) - Revised Withholding Tax Table 2023-Present.</p>
              </div>
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                <span class="font-bold text-slate-900 block text-sm">SSS Social Security</span>
                <p class="text-slate-600">RA 11199 - 14% Contribution (9.5% ER / 4.5% EE) capped at ₱30k MSC.</p>
              </div>
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                <span class="font-bold text-slate-900 block text-sm">PhilHealth National Health</span>
                <p class="text-slate-600">RA 11223 - 5% Premium Rate (2.5% ER / 2.5% EE) floor ₱10k, ceiling ₱100k.</p>
              </div>
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                <span class="font-bold text-slate-900 block text-sm">HDMF Pag-IBIG Fund</span>
                <p class="text-slate-600">Circular No. 460 - 2% Premium with ₱10,000 Cap (₱200 ER / ₱200 EE Max).</p>
              </div>
            </div>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- VIEW 2: PEOPLE MODULES (When a People sub-item is clicked) -->
        <!-- ======================================================== -->
        <div v-else-if="isPeopleModule(activeItem)" class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-[11px] font-mono text-slate-400 uppercase tracking-wider">PEOPLE & PERSONNEL</div>
              <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ getModuleTitle(activeItem) }}</h1>
            </div>
            <button
              type="button"
              class="px-4 py-2 bg-black hover:bg-slate-800 text-white font-semibold rounded-xl text-xs flex items-center gap-2 shadow-sm transition"
              @click="openAddUserModal"
            >
              <span>+ Add Entry</span>
            </button>
          </div>

          <!-- Employees or 201 File -->
          <div v-if="activeItem === 'employees' || activeItem === 'pds_201'" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
              <span class="text-xs font-bold text-slate-700">Official Personnel 201 Directory</span>
              <span class="text-xs text-slate-400 font-mono">{{ peopleData.employees?.length || 0 }} Records</span>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-100">
                  <tr>
                    <th class="px-4 py-3">Emp #</th>
                    <th class="px-4 py-3">Full Name</th>
                    <th class="px-4 py-3">Department</th>
                    <th class="px-4 py-3">Position / Rank</th>
                    <th class="px-4 py-3">Contract</th>
                    <th class="px-4 py-3">Monthly Base</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-center">PDS / 201</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="emp in peopleData.employees" :key="emp.id" class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-mono text-slate-600">{{ emp.employee_number }}</td>
                    <td class="px-4 py-3 font-bold text-slate-900">{{ emp.name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ emp.department }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ emp.position }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-slate-100 font-mono text-[10px]">{{ emp.contract_type }}</span></td>
                    <td class="px-4 py-3 font-mono font-semibold text-slate-900">₱{{ emp.base_rate }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ emp.status }}</span></td>
                    <td class="px-4 py-3 text-center">
                      <button
                        type="button"
                        class="px-2.5 py-1 rounded bg-black text-white text-[11px] font-semibold hover:bg-slate-800"
                        @click="selectedPds = emp"
                      >
                        View 201
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Positions or Plantilla -->
          <div v-else-if="activeItem === 'positions' || activeItem === 'plantilla' || activeItem === 'plantilla_report'" class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
              <h2 class="text-sm font-bold text-slate-900">Itemized Plantilla of Personnel (Philippine Government & Academic Standard)</h2>
              <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                  <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-100">
                    <tr>
                      <th class="px-4 py-3">Plantilla Item</th>
                      <th class="px-4 py-3">Position Title</th>
                      <th class="px-4 py-3">Salary Grade</th>
                      <th class="px-4 py-3">Authorized Rate</th>
                      <th class="px-4 py-3">Incumbent</th>
                      <th class="px-4 py-3">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="item in peopleData.plantilla" :key="item.item_number">
                      <td class="px-4 py-3 font-mono font-bold text-slate-700">{{ item.item_number }}</td>
                      <td class="px-4 py-3 font-semibold text-slate-900">{{ item.position_title }}</td>
                      <td class="px-4 py-3 font-mono text-slate-600">{{ item.salary_grade }}</td>
                      <td class="px-4 py-3 font-mono text-slate-900 font-bold">{{ item.authorized_rate }}</td>
                      <td class="px-4 py-3 text-slate-800">{{ item.incumbent }}</td>
                      <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="item.status === 'Filled' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'">
                          {{ item.status }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Assignments -->
          <div v-else-if="activeItem === 'assignments'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
            <h2 class="text-sm font-bold text-slate-900">Faculty Academic Workload & Teaching Allocations</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div v-for="asg in peopleData.assignments" :key="asg.course_code" class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                <span class="text-xs font-mono font-bold bg-black text-white px-2 py-0.5 rounded">{{ asg.course_code }}</span>
                <h4 class="font-bold text-slate-900 text-sm">{{ asg.course_title }}</h4>
                <p class="text-xs text-slate-600">Faculty: {{ asg.faculty_name }}</p>
                <div class="text-[11px] text-slate-500 border-t border-slate-200/60 pt-2 space-y-0.5">
                  <div>Section: {{ asg.section }} • Room: {{ asg.room }}</div>
                  <div class="font-bold text-slate-800">{{ asg.total_load }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Movements & History -->
          <div v-else class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
            <h2 class="text-sm font-bold text-slate-900">Personnel Career Movements & Notice of Step Increments (NOSI)</h2>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-100">
                  <tr>
                    <th class="px-4 py-3">Employee</th>
                    <th class="px-4 py-3">Movement Type</th>
                    <th class="px-4 py-3">From</th>
                    <th class="px-4 py-3">To</th>
                    <th class="px-4 py-3">Effectivity</th>
                    <th class="px-4 py-3">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                  <tr v-for="mov in peopleData.movements" :key="mov.employee">
                    <td class="px-4 py-3 font-sans font-bold text-slate-900">{{ mov.employee }}</td>
                    <td class="px-4 py-3 font-sans text-slate-700">{{ mov.type }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ mov.from }}</td>
                    <td class="px-4 py-3 font-bold text-emerald-700">{{ mov.to }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ mov.effectivity }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800">{{ mov.status }}</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- VIEW 3: TIME & LEAVE MODULES                             -->
        <!-- ======================================================== -->
        <div v-else-if="isTimeLeaveModule(activeItem)" class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-[11px] font-mono text-slate-400 uppercase tracking-wider">TIME & ATTENDANCE</div>
              <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ getModuleTitle(activeItem) }}</h1>
            </div>
            <button
              type="button"
              class="px-4 py-2 bg-black hover:bg-slate-800 text-white font-semibold rounded-xl text-xs flex items-center gap-2 shadow-sm transition"
            >
              <span>+ Record Time / File Leave</span>
            </button>
          </div>

          <!-- DTR Logs / Attendance Exceptions -->
          <div v-if="['attendance_control', 'attendance_exceptions', 'daily_time_records', 'monthly_dtr', 'dtr_register', 'my_attendance', 'secure_attendance'].includes(activeItem)" class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
              <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700">Official Civil Service Daily Time Record (CS Form 48)</span>
                <span class="text-xs text-slate-400 font-mono">{{ timeLeaveData.dtr_logs?.length || 0 }} Biometric Events</span>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-mono">
                  <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-100 font-sans">
                    <tr>
                      <th class="px-4 py-3">Date</th>
                      <th class="px-4 py-3">Employee</th>
                      <th class="px-4 py-3">Department</th>
                      <th class="px-4 py-3">Shift</th>
                      <th class="px-4 py-3">Time In</th>
                      <th class="px-4 py-3">Time Out</th>
                      <th class="px-4 py-3">Late (Mins)</th>
                      <th class="px-4 py-3">Undertime</th>
                      <th class="px-4 py-3">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="dtr in timeLeaveData.dtr_logs" :key="dtr.id" class="hover:bg-slate-50">
                      <td class="px-4 py-3 text-slate-600">{{ dtr.date }}</td>
                      <td class="px-4 py-3 font-sans font-bold text-slate-900">{{ dtr.employee }}</td>
                      <td class="px-4 py-3 font-sans text-slate-600">{{ dtr.department }}</td>
                      <td class="px-4 py-3 font-sans text-slate-700">{{ dtr.shift }}</td>
                      <td class="px-4 py-3 text-emerald-700 font-bold">{{ dtr.time_in }}</td>
                      <td class="px-4 py-3 text-blue-700 font-bold">{{ dtr.time_out }}</td>
                      <td class="px-4 py-3" :class="dtr.tardiness_mins > 0 ? 'text-rose-600 font-bold' : 'text-slate-400'">{{ dtr.tardiness_mins }} m</td>
                      <td class="px-4 py-3 text-slate-400">{{ dtr.undertime_mins }} m</td>
                      <td class="px-4 py-3 font-sans">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="dtr.status === 'Present' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'">
                          {{ dtr.status }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Leave Management -->
          <div v-else-if="['leave_approvals', 'leave_overview', 'leave_register', 'leave_calendar', 'my_leave'].includes(activeItem)" class="space-y-6">
            <!-- Leave Balances -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
              <div v-for="bal in timeLeaveData.leave_balances" :key="bal.type" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-400 uppercase">{{ bal.type }}</span>
                <div class="text-2xl font-bold text-slate-900 font-mono">{{ bal.balance }} Days</div>
                <div class="text-[11px] text-slate-500 font-mono">Used: {{ bal.used }} • Accruing 1.25/mo</div>
              </div>
            </div>

            <!-- Leave Requests -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
              <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700">Official Application for Leave (CSC Form 6)</span>
                <span class="text-xs text-slate-400 font-mono">{{ timeLeaveData.leave_requests?.length || 0 }} Filings</span>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                  <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-100">
                    <tr>
                      <th class="px-4 py-3">Employee</th>
                      <th class="px-4 py-3">Leave Type</th>
                      <th class="px-4 py-3">Start Date</th>
                      <th class="px-4 py-3">End Date</th>
                      <th class="px-4 py-3">Days</th>
                      <th class="px-4 py-3">Reason</th>
                      <th class="px-4 py-3">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="lv in timeLeaveData.leave_requests" :key="lv.id" class="hover:bg-slate-50">
                      <td class="px-4 py-3 font-bold text-slate-900">{{ lv.employee }}</td>
                      <td class="px-4 py-3 font-semibold text-slate-700">{{ lv.leave_type }}</td>
                      <td class="px-4 py-3 font-mono text-slate-600">{{ lv.start_date }}</td>
                      <td class="px-4 py-3 font-mono text-slate-600">{{ lv.end_date }}</td>
                      <td class="px-4 py-3 font-mono font-bold">{{ lv.days_requested }}</td>
                      <td class="px-4 py-3 text-slate-500">{{ lv.reason }}</td>
                      <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="lv.status === 'Approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'">
                          {{ lv.status }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Holiday Management -->
          <div v-else-if="activeItem === 'holiday_management'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900">Philippine Proclamation Regular & Special Non-Working Holidays (2026)</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div v-for="h in timeLeaveData.holidays" :key="h.name" class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2 text-xs">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-black text-white font-mono">{{ h.type }}</span>
                <h4 class="font-bold text-slate-900 text-sm">{{ h.name }}</h4>
                <div class="font-mono text-slate-600 font-semibold">{{ h.date }}</div>
                <div class="text-[11px] text-slate-500">Premium Rate: {{ h.premium }}</div>
              </div>
            </div>
          </div>

          <!-- Overtime -->
          <div v-else-if="activeItem === 'overtime'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900">Overtime Authority Authorizations (DOLE / CSC Standard)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="ot in timeLeaveData.overtime" :key="ot.employee" class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2 text-xs font-mono">
                <div class="flex items-center justify-between">
                  <span class="font-sans font-bold text-slate-900 text-sm">{{ ot.employee }}</span>
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">{{ ot.status }}</span>
                </div>
                <div class="text-slate-600 font-sans">Project: {{ ot.task }}</div>
                <div class="text-slate-500">Date: {{ ot.date }} • Authorized Hours: {{ ot.hours }} hrs</div>
                <div class="text-emerald-700 font-bold border-t border-slate-200/80 pt-2 font-sans">Multipliers: 125% Regular OT / 130% Rest Day</div>
              </div>
            </div>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- VIEW 4: PAYROLL SUITE MODULES                            -->
        <!-- ======================================================== -->
        <div v-else-if="isPayrollModule(activeItem)" class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-[11px] font-mono text-slate-400 uppercase tracking-wider">FINANCE & COMPENSATION</div>
              <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ getModuleTitle(activeItem) }}</h1>
            </div>
            <button
              type="button"
              class="px-4 py-2 bg-black hover:bg-slate-800 text-white font-semibold rounded-xl text-xs flex items-center gap-2 shadow-sm transition"
            >
              <span>+ New Payroll Run</span>
            </button>
          </div>

          <!-- Interactive Payroll Grid Component -->
          <div v-if="['payroll_rules', 'payroll_simulator', 'payroll_runs', 'payroll_register', 'payroll_approvals', 'payroll_payslips', 'benefits'].includes(activeItem)">
            <PayrollGrid />
          </div>

          <!-- Remittances -->
          <div v-else-if="activeItem === 'remittances'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900">Philippine Statutory Authority Remittances (October 2026)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="rem in payrollSuiteData.remittances" :key="rem.form" class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-slate-900 text-sm">{{ rem.form }}</span>
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">{{ rem.period }}</span>
                </div>
                <div class="text-slate-600 font-medium">{{ rem.agency }}</div>
                <p class="text-slate-500 text-[11px]">{{ rem.description }}</p>
                <div class="border-t border-slate-200/80 pt-2 flex items-center justify-between font-mono">
                  <span>Due: {{ rem.due_date }}</span>
                  <span class="font-bold text-emerald-700 text-sm">{{ rem.amount_due }}</span>
                </div>
                <div class="text-[10px] text-slate-500">{{ rem.status }}</div>
              </div>
            </div>
          </div>

          <!-- Loans -->
          <div v-else-if="activeItem === 'employee_loans'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900">Government & Institutional Loan Amortization Ledger</h2>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-100">
                  <tr>
                    <th class="px-4 py-3">Employee</th>
                    <th class="px-4 py-3">Loan Type</th>
                    <th class="px-4 py-3">Reference #</th>
                    <th class="px-4 py-3">Principal</th>
                    <th class="px-4 py-3">Semi-Monthly Ded.</th>
                    <th class="px-4 py-3">Remaining Balance</th>
                    <th class="px-4 py-3">Term End</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                  <tr v-for="ln in payrollSuiteData.loans" :key="ln.reference_number" class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-sans font-bold text-slate-900">{{ ln.employee }}</td>
                    <td class="px-4 py-3 font-sans text-slate-700">{{ ln.loan_type }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ ln.reference_number }}</td>
                    <td class="px-4 py-3 font-semibold">{{ ln.total_loan }}</td>
                    <td class="px-4 py-3 text-rose-600 font-bold">-{{ ln.semi_monthly_ded }}</td>
                    <td class="px-4 py-3 text-emerald-700 font-bold">{{ ln.remaining_balance }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ ln.term_end }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Bank Accounts -->
          <div v-else-if="activeItem === 'bank_accounts'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900">Corporate Bank Disbursement Transmittal Accounts</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
              <div v-for="b in payrollSuiteData.bank_accounts" :key="b.bank_name" class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2">
                <span class="text-base">🏛️</span>
                <h4 class="font-bold text-slate-900 text-sm">{{ b.bank_name }}</h4>
                <div class="font-mono text-slate-600">Acct: {{ b.account_number }}</div>
                <p class="text-[11px] text-slate-500">Format: {{ b.format }}</p>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 inline-block">{{ b.status }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- VIEW 5: RECRUITMENT & PERFORMANCE MODULES (31 Modules)   -->
        <!-- ======================================================== -->
        <div v-else-if="isRecruitmentModule(activeItem)" class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-[11px] font-mono text-slate-400 uppercase tracking-wider">RECRUITMENT & PERFORMANCE SUITE</div>
              <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ getModuleTitle(activeItem) }}</h1>
            </div>
            <div class="flex items-center gap-2">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                Item: {{ activeItem }}
              </span>
              <button
                type="button"
                class="px-4 py-2 bg-black hover:bg-slate-800 text-white font-semibold rounded-xl text-xs flex items-center gap-2 shadow-sm transition"
                @click="openAddUserModal"
              >
                <span>+ Action / New Record</span>
              </button>
            </div>
          </div>

          <!-- 1. Vacancies & Recruitment & Job Postings -->
          <div v-if="['vacancies', 'recruitment', 'job_postings'].includes(activeItem)" class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
              <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700">Official Plantilla Vacancies & Position Openings</span>
                <span class="text-xs text-slate-400 font-mono">{{ recruitmentData.vacancies?.length || 0 }} Openings</span>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                  <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-100">
                    <tr>
                      <th class="px-4 py-3">Vacancy Code</th>
                      <th class="px-4 py-3">Job Title</th>
                      <th class="px-4 py-3">Department</th>
                      <th class="px-4 py-3">Plantilla Item</th>
                      <th class="px-4 py-3">Salary Grade</th>
                      <th class="px-4 py-3 text-center">Openings</th>
                      <th class="px-4 py-3 text-center">Applicants</th>
                      <th class="px-4 py-3">Status</th>
                      <th class="px-4 py-3">Deadline</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="vac in recruitmentData.vacancies" :key="vac.id" class="hover:bg-slate-50">
                      <td class="px-4 py-3 font-mono font-bold text-slate-700">{{ vac.id }}</td>
                      <td class="px-4 py-3 font-bold text-slate-900">{{ vac.title }}</td>
                      <td class="px-4 py-3 text-slate-600">{{ vac.department }}</td>
                      <td class="px-4 py-3 font-mono text-slate-500">{{ vac.plantilla_item }}</td>
                      <td class="px-4 py-3 font-semibold text-emerald-700">{{ vac.salary_grade }}</td>
                      <td class="px-4 py-3 text-center font-bold">{{ vac.openings }}</td>
                      <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700">{{ vac.applicants }}</span>
                      </td>
                      <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                          {{ vac.status }}
                        </span>
                      </td>
                      <td class="px-4 py-3 text-slate-500 font-mono">{{ vac.deadline }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Published Channels for Job Postings -->
            <div v-if="activeItem === 'job_postings'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div v-for="jp in recruitmentData.job_postings" :key="jp.title" class="p-4 rounded-xl border border-slate-200 bg-white shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">{{ jp.status }}</span>
                  <span class="text-xs text-slate-400 font-mono">{{ jp.date_posted }}</span>
                </div>
                <h4 class="font-bold text-slate-900 text-sm">{{ jp.title }}</h4>
                <div class="text-xs text-slate-600 flex flex-wrap gap-1">
                  <span v-for="ch in jp.channels" :key="ch" class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-[10px]">
                    {{ ch }}
                  </span>
                </div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                  <span class="text-slate-500">👀 {{ jp.views }} views</span>
                  <span class="font-bold text-slate-900">📥 {{ jp.applications }} applicants</span>
                </div>
              </div>
            </div>
          </div>

          <!-- 2. Requisitions -->
          <div v-else-if="activeItem === 'requisitions'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900">Departmental Personnel Requisitions & Budget Clearances</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div v-for="req in recruitmentData.requisitions" :key="req.req_number" class="p-5 rounded-xl border border-slate-200 bg-slate-50/60 space-y-3 text-xs">
                <div class="flex items-center justify-between">
                  <span class="font-mono font-bold text-slate-900 text-sm">{{ req.req_number }}</span>
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">{{ req.status }}</span>
                </div>
                <div>
                  <h4 class="font-bold text-slate-900 text-sm">{{ req.position }}</h4>
                  <p class="text-slate-500 text-[11px]">{{ req.department }} • {{ req.slots }} Slot(s)</p>
                </div>
                <p class="text-slate-600 italic">"{{ req.justification }}"</p>
                <div class="pt-2 border-t border-slate-200 flex items-center justify-between font-mono text-[11px]">
                  <span class="text-slate-500">Budget:</span>
                  <span class="font-bold text-blue-700">{{ req.budget_status }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. Applicants & ATS Pipeline -->
          <div v-else-if="['applicants', 'ats_pipeline'].includes(activeItem)" class="space-y-6">
            <!-- Pipeline stage cards -->
            <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
              <div class="p-3 bg-white rounded-xl border border-slate-200 text-center">
                <span class="text-[10px] font-bold text-slate-400 uppercase">1. Applied</span>
                <div class="text-lg font-bold text-slate-900 mt-0.5">18</div>
              </div>
              <div class="p-3 bg-white rounded-xl border border-slate-200 text-center">
                <span class="text-[10px] font-bold text-blue-600 uppercase">2. Screening</span>
                <div class="text-lg font-bold text-slate-900 mt-0.5">12</div>
              </div>
              <div class="p-3 bg-white rounded-xl border border-slate-200 text-center">
                <span class="text-[10px] font-bold text-purple-600 uppercase">3. Interview</span>
                <div class="text-lg font-bold text-slate-900 mt-0.5">6</div>
              </div>
              <div class="p-3 bg-white rounded-xl border border-slate-200 text-center">
                <span class="text-[10px] font-bold text-amber-600 uppercase">4. Assessment</span>
                <div class="text-lg font-bold text-slate-900 mt-0.5">4</div>
              </div>
              <div class="p-3 bg-white rounded-xl border border-slate-200 text-center">
                <span class="text-[10px] font-bold text-indigo-600 uppercase">5. Job Offer</span>
                <div class="text-lg font-bold text-slate-900 mt-0.5">2</div>
              </div>
              <div class="p-3 bg-white rounded-xl border border-slate-200 text-center">
                <span class="text-[10px] font-bold text-emerald-600 uppercase">6. Hired</span>
                <div class="text-lg font-bold text-emerald-700 mt-0.5">3</div>
              </div>
            </div>

            <!-- Applicants Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
              <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700">Applicant Tracking Directory</span>
                <span class="text-xs text-slate-400 font-mono">{{ recruitmentData.applicants?.length || 0 }} Candidates</span>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                  <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-100">
                    <tr>
                      <th class="px-4 py-3">Applicant ID</th>
                      <th class="px-4 py-3">Candidate Name</th>
                      <th class="px-4 py-3">Applied Position</th>
                      <th class="px-4 py-3">Current Pipeline Stage</th>
                      <th class="px-4 py-3">Rating</th>
                      <th class="px-4 py-3">Civil Service PDS Status</th>
                      <th class="px-4 py-3">Applied Date</th>
                      <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="app in recruitmentData.applicants" :key="app.id" class="hover:bg-slate-50">
                      <td class="px-4 py-3 font-mono font-bold text-slate-600">{{ app.id }}</td>
                      <td class="px-4 py-3">
                        <div class="font-bold text-slate-900">{{ app.name }}</div>
                        <div class="text-slate-400 text-[11px] font-mono">{{ app.email }}</div>
                      </td>
                      <td class="px-4 py-3 text-slate-700 font-medium">{{ app.position }}</td>
                      <td class="px-4 py-3">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                          {{ app.stage }}
                        </span>
                      </td>
                      <td class="px-4 py-3 font-mono font-bold text-amber-600">⭐ {{ app.rating }}</td>
                      <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700 font-mono">
                          {{ app.pds_status }}
                        </span>
                      </td>
                      <td class="px-4 py-3 font-mono text-slate-500">{{ app.applied_at }}</td>
                      <td class="px-4 py-3 text-right">
                        <button type="button" class="px-2.5 py-1 rounded bg-black text-white text-[11px] font-semibold hover:bg-slate-800">
                          Review File
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- 4. Interviews & Assessments -->
          <div v-else-if="['interviews', 'assessments'].includes(activeItem)" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Interviews -->
              <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                <h3 class="text-sm font-bold text-slate-900">Search Committee Panel Interviews</h3>
                <div class="space-y-3">
                  <div v-for="int in recruitmentData.interviews" :key="int.candidate" class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-slate-900">{{ int.candidate }}</span>
                      <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">{{ int.status }}</span>
                    </div>
                    <div class="text-slate-600 font-medium">{{ int.position }}</div>
                    <p class="text-slate-500 text-[11px]">Panel: {{ int.panel }}</p>
                    <div class="border-t border-slate-200/80 pt-2 flex items-center justify-between font-mono text-[11px]">
                      <span>🕒 {{ int.datetime }}</span>
                      <span class="font-semibold text-slate-700">{{ int.modality }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Assessments -->
              <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                <h3 class="text-sm font-bold text-slate-900">Teaching Demo & Technical Evaluations</h3>
                <div class="space-y-3">
                  <div v-for="ass in recruitmentData.assessments" :key="ass.candidate" class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-slate-900">{{ ass.candidate }}</span>
                      <span class="font-mono font-bold text-emerald-700 text-sm">{{ ass.score }}</span>
                    </div>
                    <div class="text-slate-700 font-medium">{{ ass.type }}</div>
                    <div class="text-slate-500 text-[11px]">Evaluator: {{ ass.evaluator }}</div>
                    <div class="border-t border-slate-200/80 pt-2 text-emerald-800 font-semibold text-[11px]">
                      ✓ {{ ass.status }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 5. Job Offers & Pre-employment Requirements & Hire/Onboarding -->
          <div v-else-if="['job_offers', 'pre_employment_reqs', 'hire_onboarding'].includes(activeItem)" class="space-y-6">
            <!-- Job Offers -->
            <div v-if="activeItem === 'job_offers'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
              <h2 class="text-sm font-bold text-slate-900">Official Institutional Job Offers & Compensation Packages</h2>
              <div v-for="jo in recruitmentData.job_offers" :key="jo.candidate" class="p-5 rounded-xl border border-slate-200 bg-slate-50/60 space-y-3 text-xs">
                <div class="flex items-center justify-between">
                  <span class="text-sm font-bold text-slate-900">{{ jo.candidate }}</span>
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">{{ jo.status }}</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 font-mono">
                  <div>
                    <span class="text-slate-400 block text-[10px] uppercase">Position</span>
                    <span class="font-bold text-slate-900 font-sans">{{ jo.position }}</span>
                  </div>
                  <div>
                    <span class="text-slate-400 block text-[10px] uppercase">Salary Grade</span>
                    <span class="font-bold text-slate-900">{{ jo.salary_grade }}</span>
                  </div>
                  <div>
                    <span class="text-slate-400 block text-[10px] uppercase">Base Monthly</span>
                    <span class="font-bold text-emerald-700">{{ jo.monthly_rate }}</span>
                  </div>
                  <div>
                    <span class="text-slate-400 block text-[10px] uppercase">Target Start</span>
                    <span class="font-bold text-slate-700">{{ jo.start_date }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pre-employment Requirements Checklist -->
            <div v-else-if="activeItem === 'pre_employment_reqs'" class="space-y-4">
              <div v-for="cand in recruitmentData.pre_employment_requirements" :key="cand.candidate" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4 text-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                  <div>
                    <h3 class="text-sm font-bold text-slate-900">{{ cand.candidate }}</h3>
                    <span class="text-slate-500 font-mono">{{ cand.position }}</span>
                  </div>
                  <span class="px-2.5 py-1 rounded-full font-bold text-[11px] bg-emerald-50 text-emerald-800 border border-emerald-200">
                    {{ cand.overall_status }}
                  </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                  <div
                    v-for="(st, req) in cand.checklist"
                    :key="req"
                    class="p-2.5 rounded-lg border border-slate-200 flex items-center justify-between"
                  >
                    <span class="text-slate-700 font-medium">{{ req }}</span>
                    <span
                      class="px-2 py-0.5 rounded text-[10px] font-bold font-mono"
                      :class="st === 'Verified' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                    >
                      {{ st }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Hire & Onboarding -->
            <div v-else-if="activeItem === 'hire_onboarding'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
              <h2 class="text-sm font-bold text-slate-900">New Personnel Onboarding & Provisioning Checklist</h2>
              <div v-for="ob in recruitmentData.onboarding" :key="ob.employee" class="p-5 rounded-xl border border-slate-200 bg-slate-50/60 space-y-3 text-xs">
                <div class="flex items-center justify-between">
                  <h4 class="font-bold text-slate-900 text-sm">{{ ob.employee }} ({{ ob.role }})</h4>
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">{{ ob.status }}</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono">
                  <div>
                    <span class="text-slate-400 block text-[10px] uppercase">ID Card</span>
                    <span class="font-bold text-emerald-700">✓ {{ ob.id_badge }}</span>
                  </div>
                  <div>
                    <span class="text-slate-400 block text-[10px] uppercase">Biometric DTR</span>
                    <span class="font-bold text-emerald-700">✓ {{ ob.zkteco_biometric }}</span>
                  </div>
                  <div>
                    <span class="text-slate-400 block text-[10px] uppercase">Institutional Email</span>
                    <span class="font-bold text-slate-800">{{ ob.institutional_email }}</span>
                  </div>
                  <div>
                    <span class="text-slate-400 block text-[10px] uppercase">Department Mentor</span>
                    <span class="font-bold text-slate-800">{{ ob.mentor }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 6. Performance Management Suite (IPCR / OPCR / Ratings) -->
          <div v-else-if="['performance_dashboard', 'performance_cycles', 'performance_templates', 'performance_plans', 'performance_reviews', 'performance_calibration', 'final_ratings', 'development_plans', 'improvement_plans', 'performance_reports', 'performance_security', 'performance_certification'].includes(activeItem)" class="space-y-6">
            <!-- Summary Stats for Performance -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-semibold text-slate-400 uppercase">Active Cycle</span>
                <div class="text-lg font-bold text-slate-900 mt-1">AY 2026-2027 1st Sem</div>
                <div class="text-[11px] text-emerald-600 font-semibold mt-1">86% Evaluations Completed</div>
              </div>
              <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-semibold text-slate-400 uppercase">PBB Top Tier Quota</span>
                <div class="text-lg font-bold text-emerald-700 mt-1">Top 10% Outstanding</div>
                <div class="text-[11px] text-slate-500 font-mono mt-1">CSC SPMS Compliant</div>
              </div>
              <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-semibold text-slate-400 uppercase">Average Faculty Score</span>
                <div class="text-lg font-bold text-slate-900 mt-1">4.76 / 5.00</div>
                <div class="text-[11px] text-purple-600 font-semibold mt-1">Very Satisfactory Grade</div>
              </div>
            </div>

            <!-- Final Ratings Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
              <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700">Official Adjudicated Performance Ratings (SPMS / IPCR)</span>
                <span class="text-xs text-slate-400 font-mono">CSC Form 87 Certified</span>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                  <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-100">
                    <tr>
                      <th class="px-4 py-3">Faculty / Personnel</th>
                      <th class="px-4 py-3">Position</th>
                      <th class="px-4 py-3">Department</th>
                      <th class="px-4 py-3">Numeric Score</th>
                      <th class="px-4 py-3">Adjectival Rating</th>
                      <th class="px-4 py-3">Performance-Based Bonus (PBB) Tier</th>
                      <th class="px-4 py-3">Certified By</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="rt in recruitmentData.final_ratings" :key="rt.employee" class="hover:bg-slate-50">
                      <td class="px-4 py-3 font-bold text-slate-900">{{ rt.employee }}</td>
                      <td class="px-4 py-3 text-slate-700 font-medium">{{ rt.position }}</td>
                      <td class="px-4 py-3 text-slate-600">{{ rt.department }}</td>
                      <td class="px-4 py-3 font-mono font-bold text-sm text-emerald-700">{{ rt.numeric_score }}</td>
                      <td class="px-4 py-3">
                        <span
                          class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                          :class="rt.adjectival === 'Outstanding' ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-emerald-100 text-emerald-900'"
                        >
                          ⭐ {{ rt.adjectival }}
                        </span>
                      </td>
                      <td class="px-4 py-3 font-mono text-slate-600">{{ rt.pbb_tier }}</td>
                      <td class="px-4 py-3 text-slate-500 text-[11px]">{{ rt.certified_by }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- 7. Template Studio & Placeholder Library -->
          <div v-else-if="['template_studio', 'placeholder_library'].includes(activeItem)" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Templates List -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
              <h3 class="text-sm font-bold text-slate-900">Standard Philippine Civil Service & Institutional Templates</h3>
              <div class="space-y-3">
                <div v-for="tpl in recruitmentData.templates" :key="tpl.id" class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1.5 text-xs">
                  <div class="flex items-center justify-between">
                    <span class="font-mono font-bold text-slate-600">{{ tpl.id }}</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-800">{{ tpl.format }}</span>
                  </div>
                  <h4 class="font-bold text-slate-900 text-sm">{{ tpl.title }}</h4>
                  <div class="text-slate-500 text-[11px]">Category: {{ tpl.category }} • Updated: {{ tpl.last_updated }}</div>
                </div>
              </div>
            </div>

            <!-- Placeholder Library -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
              <h3 class="text-sm font-bold text-slate-900">Dynamic Variable Placeholders Library</h3>
              <div class="space-y-2.5 text-xs">
                <div v-for="ph in recruitmentData.placeholders" :key="ph.tag" class="p-3 rounded-xl border border-slate-200 bg-slate-50/60 flex items-center justify-between">
                  <div>
                    <span class="font-mono font-bold text-blue-700 text-xs">{{ ph.tag }}</span>
                    <p class="text-slate-500 text-[11px] mt-0.5">{{ ph.description }}</p>
                  </div>
                  <span class="text-[11px] font-mono text-slate-400 bg-white px-2 py-1 rounded border border-slate-200">{{ ph.example }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- 8. HR Service Requests -->
          <div v-else-if="activeItem === 'hr_service_requests'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900">Employee HR Service & Document Request Tickets</h2>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-100">
                  <tr>
                    <th class="px-4 py-3">Ticket ID</th>
                    <th class="px-4 py-3">Employee</th>
                    <th class="px-4 py-3">Request Document</th>
                    <th class="px-4 py-3">Declared Purpose</th>
                    <th class="px-4 py-3">Requested On</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="sr in recruitmentData.service_requests" :key="sr.id" class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-mono font-bold text-slate-700">{{ sr.id }}</td>
                    <td class="px-4 py-3 font-bold text-slate-900">{{ sr.employee }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ sr.type }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ sr.purpose }}</td>
                    <td class="px-4 py-3 font-mono text-slate-500">{{ sr.date }}</td>
                    <td class="px-4 py-3">
                      <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                        {{ sr.status }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                      <button type="button" class="px-2.5 py-1 rounded bg-black text-white text-[11px] font-semibold hover:bg-slate-800">
                        Process
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 9. Cache & Performance & Security & Certification Fallback -->
          <div v-else class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900">{{ getModuleTitle(activeItem) }} • System Security & Operational Status</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                <span class="text-slate-400 uppercase text-[10px] font-bold">Redis Cache Cluster</span>
                <div class="font-bold text-slate-900 text-sm">{{ recruitmentData.system_cache?.redis_status || 'Operational' }}</div>
                <p class="text-slate-500 text-[11px]">Hit Rate: {{ recruitmentData.system_cache?.cache_hit_rate || '98.7%' }}</p>
              </div>
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                <span class="text-slate-400 uppercase text-[10px] font-bold">Average Query Latency</span>
                <div class="font-bold text-emerald-700 text-sm">{{ recruitmentData.system_cache?.query_avg_ms || '1.6 ms' }}</div>
                <p class="text-slate-500 text-[11px]">Fast indexed MySQL queries</p>
              </div>
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                <span class="text-slate-400 uppercase text-[10px] font-bold">Accreditation & Compliance</span>
                <div class="font-bold text-purple-700 text-sm">CSC Level II PRIME-HRM</div>
                <p class="text-slate-500 text-[11px]">RA 10173 DPA Certified</p>
              </div>
            </div>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- VIEW 6: DASHBOARD EXECUTIVE OVERVIEW                     -->
        <!-- ======================================================== -->
        <div v-else-if="activeItem === 'dashboard'" class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Executive System Dashboard</h1>
              <span class="text-xs font-mono text-slate-400">PILOT INSTITUTION OVERVIEW • ACADEMIC YEAR 2026-2027</span>
            </div>
          </div>

          <!-- 4 Summary KPI Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <button
              type="button"
              class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-left hover:border-slate-300 transition"
              @click="activeItem = 'employees'"
            >
              <span class="text-xs font-semibold text-slate-400 uppercase">Total Personnel</span>
              <div class="text-2xl font-bold text-slate-900 mt-1">7 Staff</div>
              <div class="text-[11px] text-emerald-600 font-semibold mt-1">100% 201 Verified ➔</div>
            </button>

            <button
              type="button"
              class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-left hover:border-slate-300 transition"
              @click="activeItem = 'daily_time_records'"
            >
              <span class="text-xs font-semibold text-slate-400 uppercase">Biometric DTR</span>
              <div class="text-2xl font-bold text-slate-900 mt-1">ZK-Teco Sync</div>
              <div class="text-[11px] text-blue-600 font-semibold mt-1">Terminal 1 Active ➔</div>
            </button>

            <button
              type="button"
              class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-left hover:border-slate-300 transition"
              @click="activeItem = 'payroll_runs'"
            >
              <span class="text-xs font-semibold text-slate-400 uppercase">Last Payroll Run</span>
              <div class="text-2xl font-bold text-emerald-600 mt-1">₱175,000.00</div>
              <div class="text-[11px] text-slate-500 font-mono mt-1">100% Parity Verified ➔</div>
            </button>

            <button
              type="button"
              class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-left hover:border-slate-300 transition"
              @click="activeItem = 'vacancies'"
            >
              <span class="text-xs font-semibold text-slate-400 uppercase">Recruitment & ATS</span>
              <div class="text-2xl font-bold text-slate-900 mt-1">4 Vacancies</div>
              <div class="text-[11px] text-indigo-600 font-semibold mt-1">View Candidates ➔</div>
            </button>
          </div>
        </div>
      </main>
    </div>

    <!-- PDS / 201 FILE MODAL (Philippine Civil Service Form 212 & 201 Viewer) -->
    <div v-if="selectedPds" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-fadeIn">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div>
            <h3 class="text-lg font-bold text-slate-900">201 / PDS Record: {{ selectedPds.name }}</h3>
            <span class="text-xs text-slate-500 font-mono">Employee ID: {{ selectedPds.employee_number }} • {{ selectedPds.department }}</span>
          </div>
          <button type="button" @click="selectedPds = null" class="text-slate-400 hover:text-slate-600 text-lg">
            ✕
          </button>
        </div>

        <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
          <div>
            <span class="text-slate-400 block text-[10px] uppercase">Position Title</span>
            <span class="font-bold text-slate-900">{{ selectedPds.position }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[10px] uppercase">Plantilla Code</span>
            <span class="font-mono font-bold text-slate-900">{{ selectedPds.plantilla_code }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[10px] uppercase">Monthly Base Salary</span>
            <span class="font-mono font-bold text-emerald-700 text-sm">₱{{ selectedPds.base_rate }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[10px] uppercase">Contract Nature</span>
            <span class="font-semibold text-slate-800">{{ selectedPds.contract_type }} ({{ selectedPds.salary_basis }})</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[10px] uppercase">BIR Tax Identification (TIN)</span>
            <span class="font-mono text-slate-700">{{ selectedPds.pds_info?.tin }} (Encrypted)</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[10px] uppercase">SSS / GSIS Number</span>
            <span class="font-mono text-slate-700">{{ selectedPds.pds_info?.sss }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[10px] uppercase">PhilHealth Identification #</span>
            <span class="font-mono text-slate-700">{{ selectedPds.pds_info?.philhealth }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[10px] uppercase">Pag-IBIG / HDMF MID #</span>
            <span class="font-mono text-slate-700">{{ selectedPds.pds_info?.pagibig }}</span>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
          <button
            type="button"
            class="px-4 py-2 rounded-lg bg-black text-white text-xs font-semibold hover:bg-slate-800"
            @click="selectedPds = null"
          >
            Close 201 File
          </button>
        </div>
      </div>
    </div>

    <!-- ADD USER / PARTICIPANT MODAL -->
    <div v-if="showAddModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-5 animate-fadeIn">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div>
            <h3 class="text-lg font-bold text-slate-900">Create New Account</h3>
            <span class="text-xs text-slate-500">Assign role and institution tenancy access</span>
          </div>
          <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 text-lg">
            ✕
          </button>
        </div>

        <form @submit.prevent="submitCreateUser" class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Full Name</label>
            <input
              type="text"
              v-model="newUser.name"
              required
              placeholder="e.g. Maria Santos"
              class="w-full min-h-[44px] px-3.5 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none"
            />
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Email Address</label>
            <input
              type="email"
              v-model="newUser.email"
              required
              placeholder="e.g. maria.santos@sancarlos.edu.ph"
              class="w-full min-h-[44px] px-3.5 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Assigned Role</label>
              <select
                v-model="newUser.role"
                class="w-full min-h-[44px] px-3 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none bg-white"
              >
                <option value="super-admin">Super Administrator</option>
                <option value="tenant-admin">Institution Administrator</option>
                <option value="hr-officer">Human Resources Officer</option>
                <option value="academic-head">Academic Dean / Dept Head</option>
                <option value="payroll-preparer">Payroll Officer (Preparer)</option>
                <option value="payroll-approver">Finance Approver</option>
                <option value="auditor">Auditor</option>
                <option value="employee">Faculty / Staff (ESS)</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Assigned Institution</label>
              <select
                v-model="newUser.tenant_id"
                :disabled="newUser.role === 'super-admin'"
                class="w-full min-h-[44px] px-3 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none bg-white disabled:bg-slate-100"
              >
                <option value="">Global (Superadmin only)</option>
                <option v-for="t in tenants" :key="t.id" :value="t.id">{{ t.name }} ({{ t.code }})</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Temporary Password</label>
            <input
              type="password"
              v-model="newUser.password"
              required
              minlength="8"
              placeholder="Minimum 8 characters..."
              class="w-full min-h-[44px] px-3.5 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none"
            />
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
            <button
              type="button"
              class="px-4 py-2.5 rounded-lg border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition"
              @click="showAddModal = false"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-5 py-2.5 rounded-lg bg-black text-white font-semibold hover:bg-slate-800 transition"
            >
              Create Account
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import PayrollGrid from './PayrollGrid.vue';
import EmployeeSelfService from './EmployeeSelfService.vue';

const csrfToken = computed(() => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

interface CurrentUser {
  id: number;
  name: string;
  email: string;
  role: string;
  role_name: string;
  tenant_id: string | null;
  tenant_name: string;
  avatar?: string;
  is_super_admin: boolean;
  can_manage_roles: boolean;
  can_manage_users: boolean;
  can_manage_institutions: boolean;
  can_manage_personnel: boolean;
  can_approve_payroll: boolean;
  can_prepare_payroll: boolean;
  permissions: string[];
}

interface UserRecord {
  id: number;
  name: string;
  email: string;
  role: string;
  tenant_name: string;
  tenant_id: string | null;
  is_active: boolean;
  status: string;
  created_at: string;
  last_login_at: string;
  avatar?: string;
}

interface RoleRecord {
  id: string;
  name: string;
  description: string;
  is_system: boolean;
  permissions: Record<string, boolean>;
}

interface TenantOption {
  id: string;
  name: string;
  code: string;
}

// Current logged in user
const currentUser = ref<CurrentUser>(
  (window as any).HERA_CURRENT_USER || {
    id: 1,
    name: 'Sarah Johnson',
    email: 'admin@hera.ph',
    role: 'super-admin',
    role_name: 'Super Administrator',
    tenant_name: 'Global Platform',
    tenant_id: null,
    is_super_admin: true,
    can_manage_roles: true,
    can_manage_users: true,
    can_manage_institutions: true,
    can_manage_personnel: true,
    can_approve_payroll: true,
    can_prepare_payroll: true,
    permissions: ['*'],
  }
);

// Active navigation item (Defaults to 'setting' matching the reference image)
const activeItem = ref<string>('setting');

// Dropdown expansion state (Default: recruitment open to show new modules immediately)
const openDropdowns = ref<Record<string, boolean>>({
  people: false,
  time_leave: false,
  payroll: false,
  recruitment: true,
});

function toggleDropdown(key: string) {
  openDropdowns.value[key] = !openDropdowns.value[key];
}

// ============================================
// EXACT 9 MODULES FROM USER'S "PEOPLE" IMAGE
// ============================================
const peopleModules = [
  { id: 'employees', label: 'Employees', icon: '👥' },
  { id: 'pds_201', label: 'PDS / 201 File', icon: '📄' },
  { id: 'positions', label: 'Positions', icon: '💼' },
  { id: 'plantilla', label: 'Plantilla', icon: '🏛️' },
  { id: 'plantilla_report', label: 'Plantilla Report', icon: '📊' },
  { id: 'compensation', label: 'Compensation', icon: '💳' },
  { id: 'assignments', label: 'Assignments', icon: '📋' },
  { id: 'movements_history', label: 'Movements & History', icon: '🔄' },
  { id: 'personal_changes', label: 'Personal Changes', icon: '✍️' },
];

// ==================================================
// EXACT 15 MODULES FROM USER'S "TIME & LEAVE" IMAGE
// ==================================================
const timeLeaveModules = [
  { id: 'attendance_control', label: 'Attendance Control', icon: '⏱️' },
  { id: 'attendance_exceptions', label: 'Attendance Exceptions', icon: '⚠️' },
  { id: 'daily_time_records', label: 'Daily Time Records', icon: '📝' },
  { id: 'monthly_dtr', label: 'Monthly DTR', icon: '🕒' },
  { id: 'holiday_management', label: 'Holiday Management', icon: '📅' },
  { id: 'leave_approvals', label: 'Leave Approvals', icon: '🗓️' },
  { id: 'attendance_setup', label: 'Attendance Setup', icon: '⚙️' },
  { id: 'dtr_register', label: 'DTR Register', icon: '📒' },
  { id: 'my_attendance', label: 'My Attendance', icon: '⏱️' },
  { id: 'leave_overview', label: 'Leave Overview', icon: '📊' },
  { id: 'leave_register', label: 'Leave Register', icon: '📑' },
  { id: 'leave_calendar', label: 'Leave Calendar', icon: '🗓️' },
  { id: 'my_leave', label: 'My Leave', icon: '🏖️' },
  { id: 'overtime', label: 'Overtime', icon: '⏳' },
  { id: 'secure_attendance', label: 'Secure Attendance', icon: '🔒' },
];

// ==============================================
// EXACT 10 MODULES FROM USER'S "PAYROLL" IMAGE
// ==============================================
const payrollModules = [
  { id: 'payroll_rules', label: 'Payroll Rules', icon: '📜' },
  { id: 'payroll_simulator', label: 'Payroll Simulator', icon: '🧪' },
  { id: 'payroll_runs', label: 'Payroll Runs', icon: '⚡' },
  { id: 'payroll_register', label: 'Payroll Register', icon: '📖' },
  { id: 'payroll_approvals', label: 'Payroll Approvals', icon: '✍️' },
  { id: 'payroll_payslips', label: 'Payroll Payslips', icon: '🧾' },
  { id: 'benefits', label: 'Benefits', icon: '🎁' },
  { id: 'employee_loans', label: 'Employee Loans', icon: '🏦' },
  { id: 'remittances', label: 'Remittances', icon: '⚖️' },
  { id: 'bank_accounts', label: 'Bank Accounts', icon: '💳' },
];

// ========================================================
// EXACT 31 MODULES FROM USER'S "RECRUITMENT" IMAGES 4 & 5
// ========================================================
const recruitmentModules = [
  { id: 'vacancies', label: 'Vacancies', icon: '📋' },
  { id: 'recruitment', label: 'Recruitment', icon: '🔲' },
  { id: 'requisitions', label: 'Requisitions', icon: '📑' },
  { id: 'job_postings', label: 'Job Postings', icon: '📢' },
  { id: 'applicants', label: 'Applicants', icon: '🗂️' },
  { id: 'ats_pipeline', label: 'ATS Pipeline', icon: '🔻' },
  { id: 'interviews', label: 'Interviews', icon: '💬' },
  { id: 'assessments', label: 'Assessments', icon: '☑️' },
  { id: 'job_offers', label: 'Job Offers', icon: '🤝' },
  { id: 'pre_employment_reqs', label: 'Pre-employment Requirements', icon: '📝' },
  { id: 'hire_onboarding', label: 'Hire & Onboarding', icon: '👤' },
  { id: 'reports_analytics', label: 'Reports & Analytics', icon: '📊' },
  { id: 'performance_dashboard', label: 'Performance Dashboard', icon: '🔲' },
  { id: 'performance_cycles', label: 'Performance Cycles', icon: '🔄' },
  { id: 'performance_templates', label: 'Performance Templates', icon: '📑' },
  { id: 'performance_plans', label: 'Performance Plans', icon: '📈' },
  { id: 'performance_reviews', label: 'Performance Reviews', icon: '📝' },
  { id: 'performance_calibration', label: 'Performance Calibration', icon: '⚖️' },
  { id: 'final_ratings', label: 'Final Ratings', icon: '⭐' },
  { id: 'development_plans', label: 'Development Plans', icon: '📈' },
  { id: 'improvement_plans', label: 'Improvement Plans', icon: '⚠️' },
  { id: 'performance_reports', label: 'Performance Reports', icon: '📊' },
  { id: 'performance_security', label: 'Performance Security', icon: '🛡️' },
  { id: 'performance_certification', label: 'Performance Certification', icon: '📜' },
  { id: 'template_studio', label: 'Template Studio', icon: '📄' },
  { id: 'placeholder_library', label: 'Placeholder Library', icon: '📚' },
  { id: 'hr_service_requests', label: 'HR Service Requests', icon: '📋' },
  { id: 'cache_performance', label: 'Cache & Performance', icon: '⚡' },
  { id: 'pipeline_settings', label: 'Pipeline Settings', icon: '⚙️' },
  { id: 'recruitment_security', label: 'Recruitment Security', icon: '🎯' },
  { id: 'recruitment_certification', label: 'Recruitment Certification', icon: '🏅' },
];

function isPeopleModule(id: string): boolean {
  return peopleModules.some(m => m.id === id);
}

function isTimeLeaveModule(id: string): boolean {
  return timeLeaveModules.some(m => m.id === id);
}

function isPayrollModule(id: string): boolean {
  return payrollModules.some(m => m.id === id);
}

function isRecruitmentModule(id: string): boolean {
  return recruitmentModules.some(m => m.id === id);
}

function getModuleTitle(id: string): string {
  const all = [...peopleModules, ...timeLeaveModules, ...payrollModules, ...recruitmentModules];
  const found = all.find(m => m.id === id);
  return found ? found.label : id;
}

// Sub-tabs for Settings page
const activeSubTab = ref('users');
const searchQuery = ref('');
const activeActionDropdown = ref<number | null>(null);
const showAddModal = ref(false);
const selectedPds = ref<any | null>(null);

const users = ref<UserRecord[]>([]);
const roles = ref<RoleRecord[]>([]);
const tenants = ref<TenantOption[]>([]);

// Module data containers
const peopleData = ref<any>({ employees: [], plantilla: [], positions: [], assignments: [], movements: [] });
const timeLeaveData = ref<any>({ dtr_logs: [], leave_requests: [], leave_balances: [], holidays: [], overtime: [] });
const payrollSuiteData = ref<any>({ payroll_runs: [], remittances: [], loans: [], bank_accounts: [] });
const recruitmentData = ref<any>({
  vacancies: [],
  requisitions: [],
  job_postings: [],
  applicants: [],
  interviews: [],
  assessments: [],
  job_offers: [],
  pre_employment_requirements: [],
  onboarding: [],
  performance_cycles: [],
  final_ratings: [],
  templates: [],
  placeholders: [],
  service_requests: [],
  system_cache: {},
});

const newUser = ref({
  name: '',
  email: '',
  role: 'employee',
  tenant_id: '',
  password: 'Password123!',
});

async function loadData() {
  try {
    const res = await fetch('/api/v1/admin/users');
    if (res.ok) {
      const data = await res.json();
      users.value = data.users;
      roles.value = data.roles || [];
      tenants.value = data.tenants || [];
      if (tenants.value.length > 0 && !newUser.value.tenant_id) {
        newUser.value.tenant_id = tenants.value[0].id;
      }
    }

    // Load People, Time & Leave, Payroll, Recruitment modules
    const [pRes, tRes, pyRes, recRes] = await Promise.all([
      fetch('/api/v1/modules/people'),
      fetch('/api/v1/modules/time-leave'),
      fetch('/api/v1/modules/payroll-suite'),
      fetch('/api/v1/modules/recruitment'),
    ]);

    if (pRes.ok) peopleData.value = await pRes.json();
    if (tRes.ok) timeLeaveData.value = await tRes.json();
    if (pyRes.ok) payrollSuiteData.value = await pyRes.json();
    if (recRes.ok) recruitmentData.value = await recRes.json();
  } catch (e) {
    console.error('Failed to load modules data', e);
  }
}

onMounted(() => {
  loadData();
});

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value;
  const q = searchQuery.value.toLowerCase();
  return users.value.filter(u =>
    u.name.toLowerCase().includes(q) ||
    u.email.toLowerCase().includes(q) ||
    u.role.toLowerCase().includes(q)
  );
});

function formatRoleName(roleKey: string): string {
  const found = roles.value.find(r => r.id === roleKey);
  if (found) return found.name;

  const names: Record<string, string> = {
    'super-admin': 'Admin',
    'tenant-admin': 'Admin',
    'hr-officer': 'Admin',
    'academic-head': 'Admin',
    'payroll-preparer': 'Admin',
    'payroll-approver': 'Admin',
    'auditor': 'Admin',
    'employee': 'Admin',
  };
  return names[roleKey] || 'Admin';
}

async function toggleUserAccess(user: UserRecord) {
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const res = await fetch(`/api/v1/admin/users/${user.id}/toggle-access`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token || '',
        'Accept': 'application/json',
      },
    });
    if (res.ok) {
      const data = await res.json();
      user.is_active = data.is_active;
      user.status = data.status;
    } else {
      const err = await res.json();
      alert(err.message || 'Cannot toggle access.');
    }
  } catch (e) {
    console.error(e);
  }
}

async function togglePermission(role: RoleRecord, perm: string) {
  role.permissions[perm] = !role.permissions[perm];
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    await fetch(`/api/v1/admin/roles/${role.id}/permissions`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token || '',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ permissions: role.permissions }),
    });
  } catch (e) {
    console.error(e);
  }
}

function openAddUserModal() {
  newUser.value = {
    name: '',
    email: '',
    role: 'employee',
    tenant_id: tenants.value[0]?.id || '',
    password: 'Password123!',
  };
  showAddModal.value = true;
}

async function submitCreateUser() {
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const res = await fetch('/api/v1/admin/users', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token || '',
        'Accept': 'application/json',
      },
      body: JSON.stringify(newUser.value),
    });

    if (res.ok) {
      showAddModal.value = false;
      await loadData();
      alert('User account created successfully.');
    } else {
      const data = await res.json();
      alert(data.message || 'Validation error while creating user.');
    }
  } catch (e) {
    console.error(e);
  }
}

function viewProfile(user: UserRecord) {
  activeActionDropdown.value = null;
  alert(`User Profile: ${user.name}\nRole: ${formatRoleName(user.role)}\nEmail: ${user.email}\nStatus: ${user.status}`);
}

function editUser(user: UserRecord) {
  activeActionDropdown.value = null;
  alert(`Edit mode for user: ${user.name}`);
}

async function deleteUser(user: UserRecord) {
  activeActionDropdown.value = null;

  if (!confirm(`Are you sure you want to delete user ${user.name}?`)) {
    return;
  }

  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const res = await fetch(`/api/v1/admin/users/${user.id}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token || '',
        'Accept': 'application/json',
      },
    });

    if (res.ok) {
      await loadData();
      alert('User account removed.');
    } else {
      const data = await res.json();
      alert(data.message || 'Cannot delete user.');
    }
  } catch (e) {
    console.error(e);
  }
}
</script>
