<?php

namespace Tests\Unit;

use App\Models\RendezVous;
use PHPUnit\Framework\TestCase;

class RendezVousStatusNormalizationTest extends TestCase
{
    public function test_status_normalization_maps_legacy_variants(): void
    {
        $this->assertSame('a_venir', RendezVous::normalizeStatus('programme'));
        $this->assertSame('a_venir', RendezVous::normalizeStatus('confirmé'));
        $this->assertSame('en_attente', RendezVous::normalizeStatus('salle attente'));
        $this->assertSame('en_soins', RendezVous::normalizeStatus('en consultation'));
        $this->assertSame('vu', RendezVous::normalizeStatus('terminée'));
        $this->assertSame('absent', RendezVous::normalizeStatus('absent'));
        $this->assertSame('annule', RendezVous::normalizeStatus('annule'));
    }
}
