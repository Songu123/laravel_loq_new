#!/bin/bash

echo "=== Migration Cleanup Script ==="

# Remove duplicate/problematic migrations
echo "Removing duplicate migrations..."

# Remove old exam table migrations
rm -f database/migrations/2025_10_14_003000_create_exams_table.php
rm -f database/migrations/2025_10_14_003100_create_questions_table.php  
rm -f database/migrations/2025_10_14_003200_create_answers_table.php

# Remove drop table migrations
rm -f database/migrations/2025_10_14_004000_drop_existing_exam_tables.php
rm -f database/migrations/2025_10_14_165823_drop_exam_tables.php

# Remove duplicate role migrations
rm -f database/migrations/2025_10_14_144551_add_role_to_users_table.php
rm -f database/migrations/2025_10_14_170247_add_role_to_users_table.php
rm -f database/migrations/2025_10_14_170306_add_role_to_users_table.php

echo "Cleanup completed!"
echo "Remaining migrations:"
ls -la database/migrations/

echo ""
echo "Now run: php artisan migrate:fresh --seed"