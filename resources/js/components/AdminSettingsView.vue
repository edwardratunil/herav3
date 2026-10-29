<template>
  <div class="flex h-screen bg-[#F8F9FA] text-slate-800 antialiased font-sans overflow-hidden">
    <!-- 1. LEFT SIDEBAR (Matching Reference Design) -->
    <aside class="w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between shrink-0 select-none">
      <div>
        <!-- Logo (Matching "SETUP LOGO" in reference) -->
        <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-100">
          <div class="w-8 h-8 rounded-lg bg-black text-white flex items-center justify-center font-black text-lg">
            ✱
          </div>
          <div>
            <span class="font-bold tracking-tight text-slate-900 text-sm uppercase block leading-none">SETUP LOGO</span>
            <span class="text-[10px] text-slate-400 font-mono">HERA PLATFORM</span>
          </div>
        </div>

        <!-- Navigation Menu -->
        <div class="px-4 py-6 space-y-6">
          <div>
            <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">MENU</span>
            <nav class="mt-2 space-y-1">
              <!-- Dashboard -->
              <button
                type="button"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition"
                :class="currentSection === 'payroll' ? 'text-slate-900 bg-slate-100 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100/70'"
                @click="currentSection = 'payroll'"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
              </button>

              <!-- Intake / Institutions -->
              <button
                type="button"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition"
                :class="currentSection === 'institutions' ? 'text-slate-900 bg-slate-100 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100/70'"
                @click="currentSection = 'institutions'"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Intake</span>
              </button>

              <!-- Participant / Personnel -->
              <button
                type="button"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition"
                :class="currentSection === 'personnel' ? 'text-slate-900 bg-slate-100 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100/70'"
                @click="currentSection = 'personnel'"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Participant</span>
              </button>

              <!-- Setting (Active item matching reference image) -->
              <button
                type="button"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition"
                :class="currentSection === 'settings' ? 'text-slate-900 bg-slate-100 shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100/70'"
                @click="currentSection = 'settings'"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Setting</span>
              </button>
            </nav>
          </div>
        </div>
      </div>

      <!-- Bottom Help Menu -->
      <div class="p-4 border-t border-slate-100">
        <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">HELP</span>
        <div class="mt-2 space-y-1">
          <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>User Guide</span>
          </a>
          <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Suport</span>
          </a>
        </div>
      </div>
    </aside>

    <!-- 2. MAIN WORKSPACE -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- TOP BAR (Exact layout from reference image) -->
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

        <!-- ========================================== -->
        <!-- SECTION: SETTINGS (Matching Reference Image) -->
        <!-- ========================================== -->
        <div v-if="currentSection === 'settings'" class="space-y-6">
          <!-- Page Title & Top Actions (Matching reference layout) -->
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
              @click="activeSubTab = 'organization'"
            >
              <span>📁</span>
              <span>Organization</span>
            </button>

            <button
              type="button"
              class="px-3.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition"
              :class="activeSubTab === 'users' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-800'"
              @click="activeSubTab = 'users'"
            >
              <span>👤</span>
              <span>User & Permissions</span>
            </button>

            <button
              type="button"
              class="px-3.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition"
              :class="activeSubTab === 'roles' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-800'"
              @click="activeSubTab = 'roles'"
            >
              <span>🔐</span>
              <span>Roles & Role Access</span>
            </button>

            <button
              type="button"
              class="px-3.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition"
              :class="activeSubTab === 'compliance' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-800'"
              @click="activeSubTab = 'compliance'"
            >
              <span>⚖️</span>
              <span>Compliance</span>
            </button>
          </div>

          <!-- TAB 1: USER DETAILS TABLE (EXACT REFERENCE DESIGN) -->
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

            <!-- The Data Table (Matching Reference Columns and Styling) -->
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
                    <!-- User Name with Avatar & Subtitle -->
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

          <!-- TAB 2: ROLES & ROLE ACCESS MATRIX -->
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

                  <!-- Permission Checklist -->
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

          <!-- TAB 3: ORGANIZATION -->
          <div v-if="activeSubTab === 'organization'" class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <div>
                <h2 class="text-base font-bold text-slate-900">Registered Campuses & Tenants</h2>
                <p class="text-xs text-slate-500">Manage institutions, campus branches, and academic terms.</p>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-2">
              <div v-for="t in tenants" :key="t.id" class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                <span class="text-xs font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full inline-block">{{ t.code }}</span>
                <h3 class="font-bold text-slate-900 text-sm">{{ t.name }}</h3>
                <p class="text-xs text-slate-500">Status: Active School Organization</p>
              </div>
            </div>
          </div>

          <!-- TAB 4: COMPLIANCE -->
          <div v-if="activeSubTab === 'compliance'" class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <div>
                <h2 class="text-base font-bold text-slate-900">Philippine Statutory Compliance Matrix</h2>
                <p class="text-xs text-slate-500">Active legislative and administrative policies in effect across institution types.</p>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 text-xs">
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                <span class="font-bold text-slate-900 block text-sm">BIR Withholding Tax</span>
                <p class="text-slate-600">RA 10963 (TRAIN Law) - Revised Withholding Tax Table 2023-Present. Pure decimal bracket tiering with zero tax up to ₱20,833/month.</p>
              </div>
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                <span class="font-bold text-slate-900 block text-sm">SSS Social Security</span>
                <p class="text-slate-600">RA 11199 (Social Security Act of 2018) - 14% Contribution Rate (9.5% ER / 4.5% EE) capped at ₱30,000 MSC, with Mandatory Provident Fund (WISP).</p>
              </div>
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                <span class="font-bold text-slate-900 block text-sm">PhilHealth National Health Insurance</span>
                <p class="text-slate-600">RA 11223 (Universal Health Care) - 5% Premium Rate (2.5% ER / 2.5% EE) on monthly basic salary with floor ₱10,000 and ceiling ₱100,000.</p>
              </div>
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                <span class="font-bold text-slate-900 block text-sm">HDMF Pag-IBIG Fund</span>
                <p class="text-slate-600">HDMF Circular No. 460 - 2% Premium with ₱10,000 Cap (₱200 ER / ₱200 EE Max) effective Feb 2024.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION: DASHBOARD / PAYROLL -->
        <div v-else-if="currentSection === 'payroll'" class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Semi-Monthly Payroll Management</h1>
              <span class="text-xs font-mono text-slate-400">HERA PLATFORM ADMIN • PILOT RECONCILIATION & DISBURSEMENT</span>
            </div>
          </div>
          <PayrollGrid />
        </div>

        <!-- SECTION: PARTICIPANT / PERSONNEL -->
        <div v-else-if="currentSection === 'personnel'" class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Faculty & Personnel Self-Service</h1>
              <span class="text-xs font-mono text-slate-400">TEACHING WORKLOAD, DTR LOGS & ENCRYPTED PAYSLIPS</span>
            </div>
          </div>
          <EmployeeSelfService />
        </div>

        <!-- SECTION: INTAKE / INSTITUTIONS -->
        <div v-else-if="currentSection === 'institutions'" class="space-y-6">
          <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <div>
                <h2 class="text-base font-bold text-slate-900">Registered Educational Institutions</h2>
                <span class="text-xs text-slate-500">Multi-tenant school organizations and branch campuses</span>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
              <div v-for="t in tenants" :key="t.id" class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                <span class="text-xs font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full inline-block">{{ t.code }}</span>
                <h3 class="font-bold text-slate-900 text-sm">{{ t.name }}</h3>
                <p class="text-xs text-slate-500">Status: Active School Organization</p>
              </div>
            </div>
          </div>
        </div>
      </main>
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

// Current logged in user injected from Blade
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

// Default to Settings view matching reference design
const currentSection = ref<string>('settings');

const activeSubTab = ref('users');
const searchQuery = ref('');
const activeActionDropdown = ref<number | null>(null);
const showAddModal = ref(false);

const users = ref<UserRecord[]>([]);
const roles = ref<RoleRecord[]>([]);
const tenants = ref<TenantOption[]>([]);

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
  } catch (e) {
    console.error('Failed to load users', e);
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
