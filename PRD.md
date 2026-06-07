## PAYROLL APPLICATIONS.

### Stack
- laravel vuejs starterkit
- database: sqlite
- tailwindcss
- adminpanel: laravel vuejs starterkit ( shadcn-vue + tailwindcss)

### Database

[karyawans]
 - id
 - staff_id (unique)
 - name
 - position
 - salary
 - bank_name
 - bank_account
 - bank_account_name
 - npwp (nullable)

[slips]
- id
- karyawan_id (foreign key to karyawans.id)
- periode_start (YYYY-MM)
- periode_end (YYYY-MM)
- total_salary (auto calculate from basic_salary + overtime_salary + meal_allowance + transportation_allowance + bonus_salary - total_deduction)
- total_deduction (auto calculate from late_deduction + absence_deduction + damaged_cost + other_deduction)
- basic_salary
- overtime_salary (default: 0)
- meal_allowance (default: 0)
- transportation_allowance (default: 0)
- bonus_salary (default: 0)
- bonus_notes (nullable)
- late_deduction (default: 0)
- absence_deduction (default: 0)
- damaged_cost (default: 0)
- other_deduction (default: 0)
- other_deduction_notes (nullable)
- status (PAID,DRAFT,OVERDUE,UNPAID,PARTIALLY_PAID)
- paid_at (nullable)
- paid_by (nullable)
- created_at
- updated_at


### Features
- slip generation
- karyawans CRUD
- reporting: ( Total Outcome for salary, Total karyawans, total outcome salary monthly)
- Slip generation per karyawan_id and periode_start + periode_end ( default status is DRAFT )
- Header action in slips table "Download Draft Data (PDF), can show the total salary name, bank name, bank account, bank account name, npwp (nullable), total salary"
- In slip form there is a button "Mark as Paid", so after user click it, status will change to PAID, and the date will be saved to paid_at and paid_by ( can multiple in tables )
- In slips table there is a button "Paid List", click it will go to paid list page
- In paid list page there is a button "Download Paid Data (PDF), can show the total salary name, bank name, bank account, bank account name, npwp (nullable), total salary"
