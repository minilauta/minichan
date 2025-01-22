<?php

namespace minichan\core;

interface Module
{
	public function register(Router &$router): void;
}
