#!/bin/bash
sudo rsync -avz /var/lib/jenkins/workspace/DeshwalNew/intact/.htaccess   /var/www/html/deshwal/.htaccess                    
sudo rsync -avz /var/lib/jenkins/workspace/DeshwalNew/intact/backend/config/main-local.php /var/www/html/deshwal/backend/config/main-local.php
sudo rsync -avz /var/lib/jenkins/workspace/DeshwalNew/intact/frontend/config/main-local.php /var/www/html/deshwal/frontend/config/main-local.php
sudo rsync -avz /var/lib/jenkins/workspace/DeshwalNew/intact/backend/web/docuploads/ /var/www/html/deshwal/backend/web/docuploads/
sudo rsync -avz /var/lib/jenkins/workspace/DeshwalNew/intact/backend/web/uploads/ /var/www/html/deshwal/backend/web/uploads/
sudo rsync -avz /var/lib/jenkins/workspace/DeshwalNew/intact/backend/runtime/ /var/www/html/deshwal/backend/runtime/
sudo rsync -avz /var/lib/jenkins/workspace/DeshwalNew/intact/frontend/runtime/ /var/www/html/deshwal/frontend/runtime/
sudo rsync -avz /var/lib/jenkins/workspace/DeshwalNew/intact/common/config/main-local.php /var/www/html/deshwal/common/config/main-local.php
sudo rsync -avz /var/lib/jenkins/workspace/DeshwalNew/intact/vendor/ /var/www/html/deshwal/vendor/ 
sudo rsync -avz /var/lib/jenkins/workspace/DeshwalNew/intact/api/comman.inc.php /var/www/html/deshwal/api/comman.inc.php 
sudo chown -R root:root /var/www/html/deshwal
sudo chmod -R 2775 /var/www/html/deshwal
sudo chmod 2777 /var/www/html/deshwal/api/exports/
#sudo chown -R root:root /data/deshwal
#sudo chmod -R 2775 /data/deshwal
