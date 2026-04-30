<?php
return [
    'adminEmail' => 'admin@example.com',
    //this param uses in contact module to reset password when user request reset password for customer portal
    //added by ptpatel on date 05-09-2025
    'user.passwordResetTokenExpire' => (3600 * 12),
];
