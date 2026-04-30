<?php
$hash = '$2y$13$/41sq4ufWkElm6gQ28SKneT2JDehcO9IA9tlGP7JcHYcnlyryxxwy';
echo "Matches password123? " . (password_verify('password123', $hash) ? 'yes' : 'no') . "\n";
echo "Matches password? " . (password_verify('password', $hash) ? 'yes' : 'no') . "\n";
