-- Adminer 4.3.0 PostgreSQL dump

\connect "www";

DROP TABLE IF EXISTS "products";
CREATE SEQUENCE "products_Id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."products" (
    "Id" integer DEFAULT nextval('"products_Id_seq"'),
    "Name" character varying(256),
    "Description" character varying(4096),
    "Quantity" integer,
    "Image" character varying(512) DEFAULT ,
    "Price" real DEFAULT 0
) WITH (oids = false);

INSERT INTO "products" ("Id", "Name", "Description", "Quantity", "Image", "Price") VALUES
(8,	'Headphones',	'Headphones (or head-phones in the early days of telephony and radio) are a pair of small loudspeaker drivers that are designed to be worn on or around the head over a user''s ears. They are electroacoustic transducers, which convert an electrical signal to a corresponding sound in the user''s ear.',	7,	'http://lorempixel.com/300/300/technics/5',	'4.99'),
(4,	'iPad',	'iPad (/ˈaɪpæd/ EYE-pad) is a line of tablet computers designed, developed and marketed by Apple Inc., which run the iOS mobile operating system. The first iPad was released on April 3, 2010; the most recent iPad models are the 9.7-inch (25 cm) iPad Pro released on March 31, 2016 and the iPad Mini 4, released on September 9, 2015. The user interface is built around the device''s multi-touch screen, including a virtual keyboard. The iPad includes built-in Wi-Fi and cellular connectivity on select models.',	10,	'http://lorempixel.com/300/300/technics/1',	'800'),
(5,	'Gramophone',	'The phonograph is a device invented in 1877 for the mechanical recording and reproduction of sound. In its later forms it is also called a gramophone (as a trademark since 1887, as a generic name in the UK since 1910). The sound vibration waveforms are recorded as corresponding physical deviations of a spiral groove engraved, etched, incised, or impressed into the surface of a rotating cylinder or disc, called a "record".',	2,	'http://lorempixel.com/300/300/technics/2',	'69'),
(6,	'Laptop',	'A laptop, often called a notebook or "notebook computer", is a small, portable personal computer with a "clamshell" form factor, an alphanumeric keyboard on the lower part of the "clamshell" and a thin LCD or LED computer screen on the upper portion, which is opened up to use the computer. Laptops are folded shut for transportation, and thus are suitable for mobile use.',	1,	'http://lorempixel.com/300/300/technics/3',	'355'),
(7,	'Motherboard',	'A motherboard (sometimes alternatively known as the mainboard, system board, baseboard, planar board or logic board,[1] or colloquially, a mobo) is the main printed circuit board (PCB) found in general purpose microcomputers and other expandable systems. It holds and allows communication between many of the crucial electronic components of a system, such as the central processing unit (CPU) and memory, and provides connectors for other peripherals.',	1,	'http://lorempixel.com/300/300/technics/4',	'199.99'),
(9,	'Dolby Headphone',	'Dolby Headphone is a technology developed by Lake Technology (Australia), that later sold marketing rights to Dolby Laboratories, sometimes referred to as Mobile Surround, which creates a virtual surround sound environment in real-time using any set of two channel stereo headphones. It takes as input either a 5.1 or a 7.1 channel signal, a Dolby Pro Logic II encoded 2 channel signal (from which 5 or 7 channels can be derived) or a stereo 2 channel signal. It sends as output a 2 channel stereo signal that includes audio cues intended to place the input channels in a simulated virtual soundstage.',	2,	'http://lorempixel.com/300/300/technics/6',	'14.5'),
(10,	'Smartphone',	'A smartphone is a mobile personal computer with an advanced mobile operating system with features useful for mobile or handheld use.[1][2][3] Smartphones, which are typically pocket-sized (as opposed to tablets, which are much larger in measurement), have the ability to place and receive voice/video calls and create and receive text messages, have personal digital assistants (such as Siri, Google Assistant, Alexa, Cortana, or Bixby ), an event calendar, a media player, video games, GPS navigation, digital camera and digital video camera.',	5,	'http://lorempixel.com/300/300/technics/7',	'199.5'),
(11,	'Raspberry Pi',	'The Raspberry Pi is a series of small single-board computers developed in the United Kingdom by the Raspberry Pi Foundation to promote the teaching of basic computer science in schools and in developing countries.[4][5][6] The original model became far more popular than anticipated,[7] selling outside of its target market for uses such as robotics.',	6,	'http://lorempixel.com/300/300/technics/8',	'25'),
(12,	'Led TV',	'An LED-backlit LCD is a flat panel display which uses LED backlighting instead of the cold cathode fluorescent (CCFL) backlighting used by most other LCDs.[1] LED-backlit LCD TVs use the same TFT LCD (thin-film-transistor liquid-crystal display) technologies as CCFL-backlit LCD TVs.',	1,	'http://lorempixel.com/300/300/technics/9',	'250'),
(13,	'MP3 Player',	'An MP3 player or Digital Audio Player is an electronic device that can play digital audio files. It is a type of Portable Media Player. The term ''MP3 player'' is a misnomer, as most players play more than the MP3 file format.',	4,	'http://lorempixel.com/300/300/technics/10',	'15');

-- 2017-05-20 13:27:32.148069+00
