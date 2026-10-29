<template>
  <div class="w-full bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 sm:p-6 space-y-6">
    <!-- Header with responsive controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
            Locked & Audited
          </span>
          <span class="text-xs text-slate-500 font-mono">Run #{{ runData.runNumber }}</span>
          <span class="text-xs font-semibold px-2 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
            Pilot Reconciled: 100% Parity
          </span>
        </div>
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">
          Semi-Monthly Faculty & Staff Payroll
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
          Period: {{ runData.periodStart }} to {{ runData.periodEnd }} (Payout: {{ runData.payoutDate }})
        </p>
      </div>

      <!-- Action buttons with minimum 44px touch targets -->
      <div class="flex flex-wrap items-center gap-2">
        <select
          v-model="selectedBank"
          class="min-h-[44px] px-3 py-2 text-xs font-semibold bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200"
        >
          <option value="landbank">Landbank (weAccess / Findes)</option>
          <option value="bdo">BDO Corporate Payroll</option>
          <option value="bpi">BPI BizLink</option>
          <option value="metrobank">Metrobank Direct</option>
        </select>

        <button
          type="button"
          class="min-h-[44px] px-4 py-2.5 inline-flex items-center justify-center rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition active:scale-95"
          @click="exportBankAdvice"
        >
          Export Advice File
        </button>

        <button
          type="button"
          class="min-h-[44px] px-4 py-2.5 inline-flex items-center justify-center rounded-lg text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-700 shadow-sm transition active:scale-95"
          @click="showReconModal = true"
        >
          Dual-Run Reconciliation
        </button>
      </div>
    </div>

    <!-- Dual-Run Reconciliation Snapshot Banner -->
    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-4 border border-slate-200 dark:border-slate-700/80">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">
            ✓
          </div>
          <div>
            <span class="font-bold text-slate-900 dark:text-white block">Pilot Dual-Run Reconciliation Verified</span>
            <span class="text-slate-500">2 Historical Cutoffs Reconciled against Legacy Spreadsheet • Variance: ₱0.00</span>
          </div>
        </div>
        <div class="flex items-center gap-4 text-center sm:text-right font-mono">
          <div>
            <span class="text-[10px] text-slate-400 uppercase block font-sans">Exact Matches</span>
            <span class="font-bold text-emerald-600 dark:text-emerald-400">100% (2/2)</span>
          </div>
          <div>
            <span class="text-[10px] text-slate-400 uppercase block font-sans">Material Discrepancies</span>
            <span class="font-bold text-slate-700 dark:text-slate-300">0</span>
          </div>
          <div>
            <span class="text-[10px] text-slate-400 uppercase block font-sans">Comptroller Sign-Off</span>
            <span class="font-bold text-blue-600 dark:text-blue-400">SIGNED</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Responsive Layout: Card view on Mobile (< 640px), Sticky-column Table on Tablet & Desktop (>= 640px) -->
    
    <!-- Mobile Card View -->
    <div class="block sm:hidden space-y-3">
      <div
        v-for="item in items"
        :key="item.id"
        class="border border-slate-200 dark:border-slate-800 rounded-xl p-4 bg-slate-50/50 dark:bg-slate-800/40"
      >
        <div class="flex items-start justify-between">
          <div>
            <h4 class="font-bold text-slate-900 dark:text-white text-base">
              {{ item.name }}
            </h4>
            <span class="text-xs font-mono text-slate-500">{{ item.employeeNumber }} • {{ item.department }}</span>
          </div>
          <span class="text-xs font-medium px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">
            {{ item.facultyRank || 'Staff' }}
          </span>
        </div>

        <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
          <div class="bg-white dark:bg-slate-900 p-2 rounded-lg border border-slate-100 dark:border-slate-800">
            <span class="text-slate-400 block">Gross Compensation</span>
            <span class="font-semibold text-slate-700 dark:text-slate-200 text-sm">₱{{ item.grossPay }}</span>
          </div>
          <div class="bg-white dark:bg-slate-900 p-2 rounded-lg border border-slate-100 dark:border-slate-800">
            <span class="text-slate-400 block">Total Deductions</span>
            <span class="font-semibold text-rose-600 dark:text-rose-400 text-sm">-₱{{ item.totalDeductions }}</span>
          </div>
        </div>

        <div class="mt-2.5 flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-700">
          <div>
            <span class="text-xs text-slate-500 block">Net Take-Home Pay</span>
            <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400">
              ₱{{ item.netPay }}
            </span>
          </div>
          <button
            type="button"
            class="min-h-[44px] px-3 py-2 text-xs font-medium text-emerald-700 dark:text-emerald-400 hover:underline flex items-center gap-1"
            @click="toggleDetails(item.id)"
          >
            {{ expandedId === item.id ? 'Hide Details' : 'View Breakdown' }}
          </button>
        </div>

        <!-- Expanded Drawer for Mobile -->
        <div v-if="expandedId === item.id" class="mt-3 pt-3 border-t border-dashed border-slate-200 dark:border-slate-700 text-xs space-y-1.5 animate-fadeIn">
          <div class="flex justify-between py-0.5">
            <span class="text-slate-500">Basic Earned:</span>
            <span class="font-mono">₱{{ item.basicEarned }}</span>
          </div>
          <div v-if="parseFloat(item.overloadPay) > 0" class="flex justify-between py-0.5 text-indigo-600 dark:text-indigo-400">
            <span>Faculty Overload:</span>
            <span class="font-mono">+₱{{ item.overloadPay }}</span>
          </div>
          <div class="flex justify-between py-0.5">
            <span class="text-slate-500">De Minimis (Non-taxable):</span>
            <span class="font-mono">₱{{ item.deMinimis }}</span>
          </div>
          <div v-if="parseFloat(item.tardinessDeduction) > 0" class="flex justify-between py-0.5 text-amber-600">
            <span>Tardiness / Undertime:</span>
            <span class="font-mono">-₱{{ item.tardinessDeduction }}</span>
          </div>
          <div class="flex justify-between py-0.5 text-slate-500">
            <span>SSS / GSIS EE:</span>
            <span class="font-mono">-₱{{ item.statutoryEe.sssOrGsis }}</span>
          </div>
          <div class="flex justify-between py-0.5 text-slate-500">
            <span>PhilHealth EE (2.5%):</span>
            <span class="font-mono">-₱{{ item.statutoryEe.philHealth }}</span>
          </div>
          <div class="flex justify-between py-0.5 text-slate-500">
            <span>Pag-IBIG EE:</span>
            <span class="font-mono">-₱{{ item.statutoryEe.pagIbig }}</span>
          </div>
          <div class="flex justify-between py-0.5 text-slate-500">
            <span>BIR Withholding Tax:</span>
            <span class="font-mono">-₱{{ item.withholdingTax }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Desktop / Tablet Table View with Sticky Employee Column -->
    <div class="hidden sm:block overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-lg">
      <table class="w-full text-left text-xs border-collapse">
        <thead class="bg-slate-50 dark:bg-slate-800/80 uppercase tracking-wider text-slate-600 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-700">
          <tr>
            <th scope="col" class="sticky left-0 bg-slate-50 dark:bg-slate-800 px-4 py-3 z-10 shadow-[1px_0_0_0_rgba(226,232,240,1)] dark:shadow-[1px_0_0_0_rgba(51,65,85,1)]">
              Personnel
            </th>
            <th scope="col" class="px-3 py-3">Basic Pay</th>
            <th scope="col" class="px-3 py-3">Overload Pay</th>
            <th scope="col" class="px-3 py-3">Gross Pay</th>
            <th scope="col" class="px-3 py-3 text-slate-500">SSS EE</th>
            <th scope="col" class="px-3 py-3 text-slate-500">PH EE</th>
            <th scope="col" class="px-3 py-3 text-slate-500">HDMF EE</th>
            <th scope="col" class="px-3 py-3 text-slate-500">BIR Tax</th>
            <th scope="col" class="px-3 py-3 text-rose-600">Total Ded.</th>
            <th scope="col" class="px-4 py-3 text-right font-bold text-emerald-700 dark:text-emerald-400">
              Net Pay
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-mono">
          <tr
            v-for="item in items"
            :key="item.id"
            class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors"
          >
            <!-- Sticky Name Column -->
            <td class="sticky left-0 bg-white dark:bg-slate-900 px-4 py-3 font-sans z-10 shadow-[1px_0_0_0_rgba(226,232,240,1)] dark:shadow-[1px_0_0_0_rgba(51,65,85,1)]">
              <span class="font-bold text-slate-900 dark:text-white block">{{ item.name }}</span>
              <span class="text-[10px] text-slate-400 font-mono">{{ item.employeeNumber }} • {{ item.facultyRank || item.department }}</span>
            </td>
            <td class="px-3 py-3 text-slate-700 dark:text-slate-300">₱{{ item.basicEarned }}</td>
            <td class="px-3 py-3 text-indigo-600 dark:text-indigo-400 font-medium">₱{{ item.overloadPay }}</td>
            <td class="px-3 py-3 font-semibold text-slate-900 dark:text-white">₱{{ item.grossPay }}</td>
            <td class="px-3 py-3 text-slate-600 dark:text-slate-400">₱{{ item.statutoryEe.sssOrGsis }}</td>
            <td class="px-3 py-3 text-slate-600 dark:text-slate-400">₱{{ item.statutoryEe.philHealth }}</td>
            <td class="px-3 py-3 text-slate-600 dark:text-slate-400">₱{{ item.statutoryEe.pagIbig }}</td>
            <td class="px-3 py-3 text-slate-600 dark:text-slate-400">₱{{ item.withholdingTax }}</td>
            <td class="px-3 py-3 text-rose-600 dark:text-rose-400 font-medium">-₱{{ item.totalDeductions }}</td>
            <td class="px-4 py-3 text-right font-extrabold text-emerald-600 dark:text-emerald-400 text-sm">
              ₱{{ item.netPay }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Dual-Run Reconciliation Modal -->
    <div v-if="showReconModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Dual-Run Reconciliation Report</h3>
            <span class="text-xs text-slate-500">Run: #PR-2026-10-A vs Pilot Historical Payroll Register</span>
          </div>
          <button type="button" @click="showReconModal = false" class="min-h-[44px] min-w-[44px] text-slate-400 hover:text-slate-600 text-lg">
            ✕
          </button>
        </div>

        <div class="grid grid-cols-3 gap-3 text-center text-xs">
          <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800">
            <span class="text-slate-500 block">Exact Matches</span>
            <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">2 / 2</span>
          </div>
          <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
            <span class="text-slate-500 block">Rounding Variants</span>
            <span class="text-xl font-bold text-slate-700 dark:text-slate-300">0</span>
          </div>
          <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
            <span class="text-slate-500 block">Net Variance</span>
            <span class="text-xl font-black text-slate-900 dark:text-white">₱0.00</span>
          </div>
        </div>

        <div class="text-xs space-y-2 bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
          <div class="flex justify-between">
            <span class="text-slate-500">HERA Total Net Pay:</span>
            <span class="font-mono font-bold">₱35,263.45</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Legacy Historical Net Pay:</span>
            <span class="font-mono font-bold">₱35,263.45</span>
          </div>
          <div class="flex justify-between pt-2 border-t border-slate-200 dark:border-slate-700 font-semibold text-emerald-600 dark:text-emerald-400">
            <span>Reconciliation Status:</span>
            <span>100% Numerical Parity (PASSED)</span>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button
            type="button"
            class="min-h-[44px] px-5 py-2.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition"
            @click="showReconModal = false"
          >
            Acknowledge & Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface StatutoryEe {
  sssOrGsis: string;
  philHealth: string;
  pagIbig: string;
}

interface PayrollItemRow {
  id: string;
  name: string;
  employeeNumber: string;
  department: string;
  facultyRank?: string;
  basicEarned: string;
  overloadPay: string;
  deMinimis: string;
  tardinessDeduction: string;
  grossPay: string;
  statutoryEe: StatutoryEe;
  withholdingTax: string;
  totalDeductions: string;
  netPay: string;
}

const runData = ref({
  runNumber: 'PR-2026-10-A',
  periodStart: 'Oct 01, 2026',
  periodEnd: 'Oct 15, 2026',
  payoutDate: 'Oct 20, 2026',
});

const selectedBank = ref('landbank');
const showReconModal = ref(false);

const items = ref<PayrollItemRow[]>([
  {
    id: 'item-1',
    name: 'Luna, Antonio P.',
    employeeNumber: 'FAC-2026-01',
    department: 'College of Arts & Sciences',
    facultyRank: 'Associate Professor',
    basicEarned: '20000.00',
    overloadPay: '2550.00',
    deMinimis: '1000.00',
    tardinessDeduction: '0.00',
    grossPay: '23550.00',
    statutoryEe: {
      sssOrGsis: '787.50',
      philHealth: '500.00',
      pagIbig: '100.00',
    },
    withholdingTax: '1836.60',
    totalDeductions: '3224.10',
    netPay: '20325.90',
  },
  {
    id: 'item-2',
    name: 'Silang, Gabriela M.',
    employeeNumber: 'FAC-2026-02',
    department: 'Basic Education Dept',
    facultyRank: 'Instructor II',
    basicEarned: '15000.00',
    overloadPay: '1275.00',
    deMinimis: '500.00',
    tardinessDeduction: '0.00',
    grossPay: '16775.00',
    statutoryEe: {
      sssOrGsis: '675.00',
      philHealth: '375.00',
      pagIbig: '100.00',
    },
    withholdingTax: '687.45',
    totalDeductions: '1837.45',
    netPay: '14937.55',
  },
]);

const expandedId = ref<string | null>(null);

function toggleDetails(id: string) {
  expandedId.value = expandedId.value === id ? null : id;
}

function exportBankAdvice() {
  alert(`Exporting official payroll advice file for ${selectedBank.value.toUpperCase()} with SHA-256 control checksums.`);
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>
