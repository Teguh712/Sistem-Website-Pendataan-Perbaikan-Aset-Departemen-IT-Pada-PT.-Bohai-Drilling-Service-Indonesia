<?php
function formatRoleName($role)
{
              $roleNames = [
                            'admin' => 'Admin',
                            'staff_rig_18' => 'Staff Rig 18',
                            'staff_rig_19' => 'Staff Rig 19',
                            'staff_rig_21' => 'Staff Rig 21',
                            'staff_rig_27' => 'Staff Rig 27',
                            'staff_rig_28' => 'Staff Rig 28',
                            'staff_rig_29' => 'Staff Rig 29',
                            'staff_hrd' => 'Departemen HRD',
                            'staff_hse' => 'Departemen HSE',
                            'staff_maintenance' => 'Departemen Maintenance',
                            'staff_transport' => 'Departemen Transport',
                            'staff_logistic' => 'Departemen Logistic'
              ];

              return $roleNames[$role] ?? 'Tidak Diketahui';
}
