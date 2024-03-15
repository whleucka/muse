<?php

namespace Nebula\Framework\Database\Interface;

interface Migration
{
    public function up();
    public function down();
}
