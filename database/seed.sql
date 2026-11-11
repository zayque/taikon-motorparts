-- ----------------------------------------------------------------------
-- Taikon Motorparts — starter catalogue.
-- Prices are sample values; edit them in the admin panel.
-- Admin accounts are NOT seeded here: create one with
--   php bin/create-admin.php
-- ----------------------------------------------------------------------

SET NAMES utf8mb4;

INSERT INTO categories (id, name, slug, description, icon, sort_order) VALUES
(1, 'Spare Parts',      'spare-parts',      'OEM and performance parts — brakes, drivetrain, filters and engine.', 'cog',     1),
(2, 'Accessories',      'accessories',      'Mirrors, lighting, covers, crash protection and styling.',           'sparkles',2),
(3, 'Riding Gear',      'riding-gear',      'Helmets, jackets, gloves and rain gear built for Malaysian roads.',  'helmet',  3),
(4, 'Automotive Tools', 'automotive-tools', 'Workshop-grade tools for home maintenance.',                         'hammer',  4);

INSERT INTO products (category_id, name, slug, sku, brand, fits, description, price, compare_price, stock, is_featured) VALUES
-- Spare parts
(1, 'Front Brake Pads (Sintered)',   'front-brake-pads-sintered',   'SP-BRK-001', 'EBC',      'Yamaha Y15ZR, LC135, NVX155',
 'Sintered metal brake pads with consistent bite in wet and dry conditions. Long-wearing compound, ideal for daily commuting and spirited weekend rides.', 45.00, 55.00, 40, 1),
(1, 'Drive Chain 428H x 120L',        'drive-chain-428h-120l',       'SP-CHN-002', 'DID',      'Most 110–150cc kapcai and underbones',
 'Heavy-duty 428H drive chain, pre-stretched and pre-lubricated. Includes clip master link.', 68.00, NULL, 25, 1),
(1, 'Sprocket Set 14T/42T',           'sprocket-set-14t-42t',        'SP-SPR-003', 'Osaki',    'Yamaha LC135 / Y15ZR',
 'Hardened steel front and rear sprocket set for smooth power delivery and long life.', 85.00, NULL, 18, 0),
(1, 'Oil Filter',                     'oil-filter-hf',               'SP-FLT-004', 'Hiflofiltro','Honda RS150R, Kawasaki Z250, Benelli TNT',
 'High-flow oil filter with resin-impregnated media and anti-drain-back valve.', 22.00, NULL, 60, 0),
(1, 'Spark Plug Iridium',             'spark-plug-iridium',          'SP-PLG-005', 'NGK',      'Universal fitment — check gap spec',
 'Iridium-tipped spark plug for stronger ignition, easier cold starts and better fuel economy.', 38.00, NULL, 75, 0),
(1, 'Adjustable Rear Shock 320mm',    'adjustable-rear-shock-320mm', 'SP-SHK-006', 'YSS',      'SYM Sport Bonus SR, Honda Wave',
 'Preload and rebound adjustable rear shock absorber. A popular upgrade for comfort and cornering.', 289.00, 329.00, 8, 1),

-- Accessories
(2, 'LED Projector Headlight',        'led-projector-headlight',     'AC-LGT-001', 'Kozi',     'Yamaha Y15ZR',
 'Plug-and-play LED projector headlight with sharp cut-off line and daytime running light.', 159.00, 189.00, 12, 1),
(2, 'Bar-End Mirror (Pair)',          'bar-end-mirror-pair',         'AC-MIR-002', 'Taikon','Universal 22mm handlebar',
 'CNC-machined aluminium bar-end mirrors with anti-glare glass.', 75.00, NULL, 20, 0),
(2, 'Frame Slider Kit',               'frame-slider-kit',            'AC-SLD-003', 'GSG',      'Kawasaki Z1000, Z900',
 'Delrin frame sliders with aluminium mounting brackets to protect your frame and engine in a drop.', 210.00, NULL, 6, 0),
(2, 'Slip-On Exhaust Carbon',         'slip-on-exhaust-carbon',      'AC-EXH-004', 'Taikon Custom','Kawasaki Z1000 (2014+)',
 'Carbon sleeve slip-on with stainless mid-pipe. Deeper tone and reduced weight. Removable dB killer included.', 1150.00, 1290.00, 3, 1),
(2, 'Waterproof Bike Cover',          'waterproof-bike-cover',       'AC-CVR-005', 'Taikon',   'Up to 250cc',
 'Heat-resistant, UV-protected cover with elastic hem and lock holes.', 49.00, NULL, 30, 0),

-- Riding gear
(3, 'Full-Face Helmet SIRIM',         'full-face-helmet-sirim',      'RG-HLM-001', 'MHR',      'Sizes M, L, XL',
 'SIRIM-certified full-face helmet with anti-fog visor, removable liner and quick-release buckle.', 239.00, 279.00, 14, 1),
(3, 'Mesh Riding Jacket',             'mesh-riding-jacket',          'RG-JKT-002', 'Taikon','Sizes S – XXL',
 'Breathable mesh jacket for hot weather, with CE-rated shoulder and elbow armour.', 320.00, NULL, 9, 0),
(3, 'Touchscreen Riding Gloves',      'touchscreen-riding-gloves',   'RG-GLV-003', 'Taikon','Sizes S – XL',
 'Short-cuff gloves with knuckle protector and touchscreen-compatible fingertips.', 65.00, NULL, 22, 0),
(3, 'Two-Piece Raincoat',             'two-piece-raincoat',          'RG-RAN-004', 'Kappa',    'Sizes M – XXL',
 'Fully waterproof PVC jacket and pants with reflective piping for monsoon season.', 55.00, NULL, 35, 0),

-- Tools
(4, 'Torque Wrench 3/8"',             'torque-wrench-3-8',           'TL-TRQ-001', 'Tekiro',   '10–60 Nm',
 'Click-type torque wrench for accurate tightening of engine and chassis bolts.', 145.00, NULL, 7, 1),
(4, 'Chain Cleaning Kit',             'chain-cleaning-kit',          'TL-CHN-002', 'Taikon','All chain drives',
 'Three-sided chain brush, degreaser and chain lube in one kit.', 42.00, NULL, 18, 0),
(4, 'Paddock Stand (Rear)',           'paddock-stand-rear',          'TL-STD-003', 'Taikon',   'Bikes with swing-arm spools',
 'Powder-coated steel rear stand with rubber-padded hooks.', 135.00, NULL, 5, 0);

INSERT INTO services (name, slug, summary, description, features, price_from, duration_minutes, icon, is_package, sort_order) VALUES
('Basic Service', 'basic-service',
 'Engine oil, filter and a 20-point safety check.',
 'Recommended every 3,000 km. We change your engine oil and filter, then inspect brakes, chain, tyres, lights and cables so you leave with peace of mind.',
 "Engine oil change\nOil filter replacement\nChain clean & adjust\n20-point safety inspection", 60.00, 45, 'wrench', 0, 1),

('Major Service & Valve Clearance', 'major-service-valve-clearance',
 'Full service plus valve clearance adjustment.',
 'For bigger bikes and higher mileage. Includes everything in the basic service plus valve clearance check and adjustment, air filter, spark plugs and coolant.',
 "Everything in Basic Service\nValve clearance check & adjust\nAir filter & spark plugs\nCoolant flush", 280.00, 180, 'gauge', 0, 2),

('Brake & Suspension Setup', 'brake-suspension-setup',
 'Brake overhaul and suspension tuned to your weight.',
 'We bleed brake lines, replace pads where needed and set sag, preload and rebound to suit your weight and riding style.',
 "Brake fluid bleed\nPad inspection & replacement\nSag & preload setup\nTest ride", 120.00, 90, 'shield', 0, 3),

('Exhaust & Accessory Fitting', 'exhaust-accessory-fitting',
 'Professional installation of parts bought from us or elsewhere.',
 'Slip-ons, full systems, lighting, sliders and more — installed correctly with the right torque and wiring.',
 "Exhaust installation\nLighting & wiring\nCrash protection fitting\nRoad-legal check (JPJ)", 50.00, 60, 'cog', 0, 4),

('Custom Build Package', 'custom-build-package',
 'From concept to road — cafe racers, street cubs and scramblers.',
 'Our signature package. We plan the build with you, source the parts, rework the engine where needed and finish the bike to your style and budget.',
 "Design consultation\nParts sourcing at trade price\nEngine refresh or rebuild\nPaint, upholstery & wiring\nRoad-legal handover", 3500.00, 120, 'sparkles', 1, 5),

('Engine Rebuild Package', 'engine-rebuild-package',
 'Top-end or full rebuild with new seals, gaskets and bearings.',
 'Brings tired engines back to life. Includes teardown, measurement, machining where required and a full reassembly with new consumables.',
 "Teardown & inspection report\nNew gaskets, seals & bearings\nBore & piston service\n500 km follow-up check", 1200.00, 120, 'hammer', 1, 6);
