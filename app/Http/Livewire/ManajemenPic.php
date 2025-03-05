<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Pic;
use Livewire\WithPagination;

class ManajemenPic extends Component
{
    use WithPagination;

    public $name, $email, $department, $pic, $employeeId;
    public $isUpdateMode = false;

    // Fungsi untuk menambahkan karyawan baru
    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'department' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
        ]);

        Pic::create([
            'name' => $this->name,
            'email' => $this->email,
            'department' => $this->department,
            'pic' => $this->pic,
        ]);

        session()->flash('message', 'Employee added successfully!');
        $this->resetFields();
    }

    // Fungsi untuk menyiapkan mode update
    public function edit($id)
    {
        $this->isUpdateMode = true;
        $employee = Pic::findOrFail($id);

        $this->employeeId = $employee->id;
        $this->name = $employee->name;
        $this->email = $employee->email;
        $this->department = $employee->department;
        $this->pic = $employee->pic;
    }

    // Fungsi untuk memperbarui data karyawan
    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $this->employeeId,
            'department' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
        ]);

        $employee = Pic::findOrFail($this->employeeId);
        $employee->update([
            'name' => $this->name,
            'email' => $this->email,
            'department' => $this->department,
            'pic' => $this->pic,
        ]);

        session()->flash('message', 'Employee updated successfully!');
        $this->resetFields();
    }

    // Fungsi untuk menghapus karyawan
    public function delete($id)
    {
        Pic::findOrFail($id)->delete();
        session()->flash('message', 'Employee deleted successfully!');
    }

    // Fungsi untuk mereset field form
    private function resetFields()
    {
        $this->name = '';
        $this->email = '';
        $this->department = '';
        $this->pic = '';
        $this->employeeId = null;
        $this->isUpdateMode = false;
    }

    public function render()
    {
        $pics = Pic::with('user','departemen')->paginate(10);
        
        return view('livewire.manajemen-pic', compact('pics'));
    }
}
