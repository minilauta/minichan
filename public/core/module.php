<?php

namespace minichan\core;

interface Module
{
	public function register_routes(Router &$router): void;
	public function get_name(): string;
	public function get_dependencies(): array;
}
