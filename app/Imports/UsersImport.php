<?php

namespace App\Imports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

use Maatwebsite\Excel\Concerns\{
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithValidation,
    SkipsOnFailure,
    Importable,
    SkipsFailures
};
use Users\Models\User;

class UsersImport implements ToModel, WithHeadingRow, WithChunkReading, WithValidation,
//    SkipsOnFailure, ShouldQueue
    SkipsOnFailure
{
    use Importable, SkipsFailures;

    public array $exceptions = [];
    public array $errors = [];
    public $index = null;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * This method is invoked for every row that fails validation.
     *
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = $failure;
        }
        // Dump all failures and stop execution
    }

    /**
     * Called on exceptions thrown in model().
     */
    public function onError(Throwable $e): void
    {
        // Laravel-Excel doesn’t pass the row here,
        // so we’ll capture exceptions inside model() instead.
    }


    public function prepareForValidation(array $row, int $index): array
    {
        $this->index = $index;
        if (
            isset($row['scfhs_expiry_date'])
            && is_numeric($row['scfhs_expiry_date'])
        ) {
            // 46275 → DateTime('2026-09-10')
            $row['scfhs_expiry_date'] = ExcelDate::excelToDateTimeObject(
                $row['scfhs_expiry_date']
            )->format('Y-m-d');
        }

        if (
            isset($row['bls_expiry_date'])
            && is_numeric($row['bls_expiry_date'])
        ) {
            // 46275 → DateTime('2026-09-10')
            $row['bls_expiry_date'] = ExcelDate::excelToDateTimeObject(
                $row['bls_expiry_date']
            )->format('Y-m-d');
        }

        if (
            isset($row['acls_expiry_date'])
            && is_numeric($row['acls_expiry_date'])
        ) {
            // 46275 → DateTime('2026-09-10')
            $row['acls_expiry_date'] = ExcelDate::excelToDateTimeObject(
                $row['acls_expiry_date']
            )->format('Y-m-d');
        }
        if (isset($row['mobile'])) {
            // cast to string so we can check length
            $mobile = (string)$row['mobile'];

            // if Excel dropped the leading 0, you'll get 9 digits starting with 5
            if (preg_match('/^[5][0-9]{8}$/', $mobile)) {
                $row['mobile'] = '0' . $mobile;
            }
        }
//
        return $row;
        // dump and halt on the very first row:

        // if you wanted to tweak data before validation, you'd return it here
        // return array_map('trim', $row);
    }

    public function rules(): array
    {

        // Pull in your existing FormRequest rules, or just copy-paste them here:
        $res = [
            'first_nameen' => array('required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'),
            'second_nameen' => array('required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'),
            'last_nameen' => array('required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'),
            'first_namear' => array('required', 'string', 'max:255', 'regex:/^[\p{Arabic}\s]+$/u'),
            'second_namear' => array('required', 'string', 'max:255', 'regex:/^[\p{Arabic}\s]+$/u'),
            'last_namear' => array('required', 'string', 'max:255', 'regex:/^[\p{Arabic}\s]+$/u'),
            'user_name' => array('required', 'max:255', 'regex:/^(?!\d)[A-Za-z0-9._]+$/', Rule::unique('users', 'user_name')),
            'email' => array('required', 'string', 'email:rfc,dns', 'max:255', Rule::unique('users', 'email')),
            'mobile' => array('required', 'regex:/^(?:\+966|0)5[0-9]{8}$/', Rule::unique('users', 'mobile')),
            'password' => array('required', 'string'),
            'national_id' => array('required', 'digits:10', 'regex:/^(?:1|2)[0-9]{9}$/', Rule::unique('users', 'national_id')),
            'city' => array('required', 'string', 'min:1', 'max:255'),
            'educational' => array('required', 'string', 'min:1', 'max:255'),
            'study' => array('required', 'string', 'min:1', 'max:255'),
            'nationality_id' => array('required'),
            'speciality' => array('required', 'string', 'min:1', 'max:255'),
            'scfhs_expiry_date' => array('required', 'string', 'min:1', 'max:255'),
            'bls_expiry_date' => array('required', 'string', 'min:1', 'max:255'),
//            'acls_expiry_date' => array('required', 'string', 'min:1', 'max:255'),
            'scfhs_license' => array('required', 'string', 'in:yes,no'),
            'bls_certification' => array('required', 'string', 'in:yes,no'),
//            'acls_certification' => array('required', 'string', 'in:yes,no'),
            'religion' => array('required', 'string', 'in:muslim,non_muslim'),
//            nationality_id religion

            // …and any other rules you listed…
        ];
        return $res;
    }

    /**
     * Map each row to a new User model.
     */
    public function model(array $row)
    {

        try {
            // Convert Excel dates to Carbon
//            $scfhsExpiry = isset($row['scfhs_expiry_date'])
//                ? ExcelDate::excelToDateTimeObject($row['scfhs_expiry_date'])
//                : null;
//            $blsExpiry = isset($row['bls_expiry_date'])
//
//
//                ? ExcelDate::excelToDateTimeObject($row['bls_expiry_date'])
//                : null;
//            $aclsExpiry = isset($row['acls_expiry_date'])
//                ? ExcelDate::excelToDateTimeObject($row['acls_expiry_date'])
//                : null;
            $user = new User([
                // note: HeadingRow will transform `last_name[ar]` → `last_name_ar`
                'first_name' => ['ar' => $row['first_namear'], 'en' => $row['first_nameen']],
                'second_name' => ['ar' => $row['second_namear'], 'en' => $row['second_nameen']],
                'last_name' => ['ar' => $row['last_namear'], 'en' => $row['last_nameen']],
                'name' => ['ar' => $row['first_namear'], 'en' => $row['first_nameen']],
                'user_name' => $row['user_name'],
                'email' => $row['email'] ?? null,
                'mobile' => $row['mobile'] ?? null,
                'code' => uniqid(),
                'password' => isset($row['password'])
                    ? Hash::make($row['password'])
                    : Hash::make(Str::random(12)),
                'national_id' => $row['national_id'] ?? null,
                'nationality_id' => $row['nationality_id'] ?? null,
                'religion' => $row['religion'] ?? null,
                'speciality' => $row['speciality'] ?? null,
                'city' => $row['city'] ?? null,
                'educational' => $row['educational'] ?? null,
                'study' => $row['study'] ?? null,
                'SCFHS_license' => $row['scfhs_license'] ?? null,
                'SCFHS_expiry_date' => $row['scfhs_expiry_date'],
                'BLS_certification' => $row['bls_certification'] ?? null,
                'BLS_expiry_date' => $row['bls_expiry_date'],
                'ACLS_certification' => $row['acls_certification'] ?? null,
                'ACLS_expiry_date' => $row['acls_expiry_date'],
                'created_by' => $this->user['id'],
                'company_id' => $this->user['company_id'],
                // leave `avatar` & `certificates`… see notes below
            ]);
            $user->assignRole("User");
            return $user;
        } catch (Throwable $e) {
            // Capture the raw row + exception message
//            $this->exceptions[] = [
//                'row' => $row,
//                'message' => $e->getMessage(),
//                '__row_index' => $this->index,
//            ];
            // Return null so import continues
            return null;
        }
    }

    /**
     * Process in chunks to save memory.
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}
