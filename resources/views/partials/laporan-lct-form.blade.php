<div class="bg-white p-5 max-h-min rounded-lg shadow-lg">
    <div class="bg-primary text-black text-center py-4 px-7 rounded-t-lg">
        <h5 class="text-xl font-bold">Finding Item Report Submission Form to PIC</h5>
    </div>

    <div class="w-full h-[2px] bg-gray-200 px-3"></div>

    <div class="p-6">
        <form action="{{ route('ehs.laporan-lct.assignToPic', ['id_laporan_lct' => $laporan->id_laporan_lct]); }}" method="POST">
            @csrf
            <div class="space-y-6">
                <!-- Form fields -->

                {{-- Area Temuan --}}
                <div class="mb-4">
                    <label for="area_temuan" class="block text-sm font-medium text-gray-700 mb-1">Finding Area</label>
                    <input type="text" class="flex justify-between w-full p-3 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="area_temuan" name="area_temuan" value="{{$laporan->area->nama_area}} - {{$laporan->detail_area}}" readonly required>
                </div>

                <!-- Kategori Temuan -->
                <div class="mb-4 flex flex-col">
                    <label for="kategori" class="block text-sm font-medium text-gray-700">
                        Finding Category <span class="text-red-500">*</span>
                    </label>
                
                    <div class="relative inline-flex" x-data="{ 
                        open: false, 
                        selected: @js($laporan->kategori->nama_kategori ?? ''), 
                        kategori: @js($kategori) 
                    }">
                        <!-- Tombol Dropdown -->
                        <button 
                            type="button" 
                            class="mt-2 w-full px-4 py-2 border border-black rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 text-left bg-white cursor-pointer"
                            @click.prevent="open = !open"
                            :aria-expanded="open"
                            aria-haspopup="true"
                        >
                            <div class="flex justify-between items-center">
                                <span x-text="selected || 'Select a Category'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            </div>
                        </button>
                
                        <!-- Hidden Input untuk Menyimpan Nilai Terpilih -->
                        <input type="hidden" name="kategori_temuan" x-model="selected" required>
                
                        <!-- Dropdown Menu -->
                        <div 
                            class="origin-top-right z-10 absolute top-full left-0 min-w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-2 overflow-hidden"
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200 transform"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-out duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            @click.away="open = false"
                            x-cloak
                        >
                            <ul class="text-sm">
                                <template x-for="category in kategori" :key="category.id">
                                    <li>
                                        <button 
                                            type="button" 
                                            class="block w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100 focus:outline-none"
                                            @click="selected = category.nama_kategori; open = false"
                                            x-text="category.nama_kategori"
                                        ></button>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>    

                {{-- Tanggal Temuan --}}
                <div class="mb-4">
                    <label for="tanggal_temuan" class="block text-sm font-medium text-gray-700 mb-1">Date of Finding</label>
                    <input type="text" 
                        value="{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->locale('en')->translatedFormat('d F Y') }}" 
                        id="tanggal_temuan" 
                        name="tanggal_temuan" 
                        class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" 
                        readonly>
                </div>     
            
                <div x-data="dropdownData()">
                    <!-- Dropdown Department -->
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select 
                            x-model="selectedDepartemen" 
                            name="departemen_id"
                            @change="updateFilteredPics()" 
                            class="w-full px-4 py-2 border border-black rounded-md bg-white shadow-sm focus:ring-2 focus:ring-blue-500 cursor-pointer"
                            :class="{ 'border-red-500': errorDepartemen }"
                            required>
                            <option value="">Select a Department</option>
                            <template x-for="dept in departemen" :key="dept.id">
                                <option :value="dept.id" x-text="dept.nama"></option>
                            </template>
                        </select>
                    </div>
                    <p x-show="errorDepartemen" class="text-red-500 text-xs mt-1">Please Select a Department.</p>
                
                    <!-- Dropdown PIC -->
                    <label class="block text-sm font-medium text-gray-700 mb-1 mt-4">PIC <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select 
                            x-model="selectedPic"
                            name="pic_id"
                            class="w-full px-4 py-2 border border-black rounded-md bg-white shadow-sm focus:ring-2 focus:ring-blue-500 cursor-pointer"
                            :class="{ 'border-red-500': errorPic }"
                            required>
                            <option value="">Select a PIC</option>
                            <template x-for="pic in filteredPics" :key="pic.pic.id">
                                <option :value="pic.pic.id" x-text="pic.pic.user.fullname"></option>
                            </template>
                            
                        </select>
                    </div>
                    <p x-show="errorPic" class="text-red-500 text-xs mt-1">Please Select a PIC.</p>
                </div>
                    
                
                {{-- Non-Conformity Finding --}}
                <div class="mb-4">
                    <label for="temuan_ketidaksesuaian" class="block text-sm font-medium text-gray-700 mb-1">Finding Item <span class="text-red-500">*</span></label>
                    <input type="text" value="{{$laporan->temuan_ketidaksesuaian}}" class="flex justify-between w-full p-3 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="temuan_ketidaksesuaian" name="temuan_ketidaksesuaian" required>
                </div>

                <div x-data="dropdownDataDate()">
                    {{-- Hazard Level --}}
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hazard Level <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select 
                            name="tingkat_bahaya"
                            x-model="tingkat_bahaya" 
                            class="w-full px-4 py-2 border border-black rounded-md bg-white shadow-sm focus:ring-2 focus:ring-blue-500 cursor-pointer"
                            :class="{ 'border-red-500': errorTingkatBahaya }"
                            required
                            @change="updateDueDateInputs()">
                            <option value="">Please Select a Hazard Level</option>
                            <template x-for="tingkat in ['Low', 'Medium', 'High']" :key="tingkat">
                                <option :value="tingkat" x-text="tingkat"></option>
                            </template>
                            <!-- <template x-for="tingkat in ['Low']" :key="tingkat">
                                <option :value="tingkat" x-text="tingkat"></option>
                            </template> -->
                        </select>
                    </div>
                    <p x-show="errorTingkatBahaya" class="text-red-500 text-xs mt-1">Please Select a Hazard Level.</p>
                </div>
                
                 <!-- Recommendation -->
                 <div class="mb-4">
                    <label for="rekomendasi" class="block text-sm font-medium text-gray-700 mb-1">Recommendation <span class="text-red-500">*</span></label>
                    <textarea class="flex justify-between w-full p-3 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="rekomendasi" name="rekomendasi" rows="4" required></textarea>
                </div>

                <!-- Due date -->
                <!-- Due date inputs -->
                <div id="due_date_section">
                    <!-- Default input for Low Hazard Level -->
                    <div id="due_date_final_section" style="display: none;">
                        <label for="due_date" class="block text-sm font-medium text-gray-700">
                            Due Date Final <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="due_date" 
                            name="due_date" 
                            class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" 
                            required
                            onclick="this.showPicker()"
                        >
                        <p id="due_date_info" class="mt-2 text-gray-600 text-sm"></p>
                    </div>

                    <!-- Temporary and Permanent Due Dates for Medium/High Hazard Level -->
                    <div id="temp_perm_due_dates" style="display: none;">
                        <label for="due_date_temp" class="block text-sm font-medium text-gray-700 mt-4">
                            Temporary Due Date <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="due_date_temp" 
                            name="due_date_temp" 
                            class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" 
                            required
                            onclick="this.showPicker()"
                        >
                        <p id="temp_due_date_info" class="mt-2 text-gray-600 text-sm"></p>

                        <label for="due_date_perm" class="block text-sm font-medium text-gray-700 mt-4">
                            Permanent Due Date <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="due_date_perm" 
                            name="due_date_perm" 
                            class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" 
                            required
                            onclick="this.showPicker()"
                        >
                        <p id="perm_due_date_info" class="mt-2 text-gray-600 text-sm"></p>
                    </div>


                <!-- Submit button -->
                <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 mt-4 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 cursor-pointer">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>




<!-- Skrip untuk Dropdown -->
<script>
function dropdownData() {
    return {
        departemen: {{ Js::from($departemen) }},
        allPics: {{ Js::from($picDepartemen) }},
        selectedDepartemen: '',
        selectedPic: '',
        filteredPics: [],
        errorDepartemen: false,
        errorPic: false,
        tingkat_bahaya: '',
        errorTingkatBahaya: false,

        updateFilteredPics() {
            if (this.selectedDepartemen) {
                this.filteredPics = this.allPics.filter(pic => pic.departemen.id == this.selectedDepartemen);
                this.selectedPic = '';
                this.errorDepartemen = false;
            } else {
                this.filteredPics = [];
            }
        },

        validateForm(event) {
            this.errorDepartemen = !this.selectedDepartemen;
            this.errorPic = !this.selectedPic;
            this.errorTingkatBahaya = !this.tingkat_bahaya;
            if (this.errorDepartemen || this.errorPic || this.errorTingkatBahaya) {
                event.preventDefault(); // Mencegah form dikirim jika ada error
            }
        }
    };
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("form").addEventListener("submit", function (e) {
        let dropdownComponent = document.querySelector("[x-data]");
        dropdownComponent.__x.$data.validateForm(e);
    });
});
</script>

<script>
// Fungsi untuk menampilkan date picker ketika input diklik
document.querySelectorAll('input[type="date"]').forEach(input => {
input.addEventListener('click', function() {
this.showPicker();
});
});
</script>

<script>
// Menangani perubahan pilihan hazard level dan menampilkan input yang relevan
function dropdownDataDate() {
return {
tingkat_bahaya: '',
errorTingkatBahaya: false,
updateDueDateInputs() {
const dueDateSection = document.getElementById("due_date_section");
const dueDateFinalSection = document.getElementById("due_date_final_section");
const tempPermDueDates = document.getElementById("temp_perm_due_dates");

if (this.tingkat_bahaya === 'Low') {
// Menampilkan due date final
dueDateFinalSection.style.display = 'block';
tempPermDueDates.style.display = 'none';
} else if (this.tingkat_bahaya === 'Medium' || this.tingkat_bahaya === 'High') {
// Menampilkan due date temporary dan permanent
dueDateFinalSection.style.display = 'none';
tempPermDueDates.style.display = 'block';
} else {
// Tidak ada pilihan hazard level
dueDateFinalSection.style.display = 'none';
tempPermDueDates.style.display = 'none';
}
}
};
}
// Fungsi untuk mendapatkan tanggal yang diformat dalam format 'YYYY-MM-DD'
function formatDate(date) {
const year = date.getFullYear();
const month = String(date.getMonth() + 1).padStart(2, '0');
const day = String(date.getDate()).padStart(2, '0');
return `${year}-${month}-${day}`;
}

// Fungsi untuk format tanggal yang lebih mudah dibaca (misalnya April 17, 2025)
function formatReadableDate(date) {
const months = [
'January', 'February', 'March', 'April', 'May', 'June',
'July', 'August', 'September', 'October', 'November', 'December'
];
const day = date.getDate();
const month = months[date.getMonth()];
const year = date.getFullYear();
return `${month} ${day}, ${year}`;
}

// Fungsi untuk mengupdate informasi tanggal
function updateDateInfo(inputId, infoId, baseDate, daysOffset) {
const inputElement = document.getElementById(inputId);
const infoElement = document.getElementById(infoId);

inputElement.addEventListener('change', function() {
const selectedDate = new Date(inputElement.value);
const readableDate = formatReadableDate(selectedDate);
infoElement.textContent = `Selected Date: ${readableDate}`;
});

// Jika input tidak diubah, set dengan default nilai (2 hari atau 1 bulan dari sekarang)
const offsetDate = new Date(baseDate);
offsetDate.setDate(offsetDate.getDate() + daysOffset);
inputElement.value = formatDate(offsetDate);
const readableDate = formatReadableDate(offsetDate);
infoElement.textContent = `This is ${daysOffset === 2 ? 'due date temporary' : 'max due date permanent'} from today: ${readableDate}`;
}

// Mengatur autofill tanggal untuk "due_date" (2 hari dari sekarang)
document.addEventListener('DOMContentLoaded', function() {
const today = new Date();

// Mengupdate due date final (2 hari dari sekarang)
updateDateInfo('due_date', 'due_date_info', today, 2);

// Mengupdate temporary due date (2 hari dari sekarang)
updateDateInfo('due_date_temp', 'temp_due_date_info', today, 2);

// Mengupdate permanent due date (1 bulan dari sekarang)
updateDateInfo('due_date_perm', 'perm_due_date_info', today, 30);
});
</script>