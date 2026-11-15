# -*- coding: utf-8 -*-
"""Le sezioni della seconda passata sulle pagine scarne (14 nov 2026), lette da arricchisci2.py: SPEC2[tema][pagina] =
[sezione, …]. I tre blocchi stanno in arricchimenti2_a/_b/_c.py."""
from arricchimenti2_a import A
from arricchimenti2_b import B
from arricchimenti2_c import C

SPEC2 = {}
for parte in (A, B, C):
    SPEC2.update(parte)
