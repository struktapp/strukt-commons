<?php

/** Pest configuration shared by the package test suite. */

pest()->extend(Tests\TestCase::class)->in('Feature', 'Unit');
