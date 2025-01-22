<?php

namespace minichan\core;

interface Plugin
{
	public function register(Router &$router): void;
}
