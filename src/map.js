var epsg4326 = new OpenLayers.Projection("EPSG:4326");
var mercator = new OpenLayers.Projection("EPSG:900913");
var latitude = 46.4983569;
var longitude = 11.3506779;
var zoom = 14;

var map = new OpenLayers.Map("map");

map.addLayer(new OpenLayers.Layer.OSM());

map.setCenter(new OpenLayers.LonLat(longitude, latitude).transform(epsg4326, mercator), zoom); 

var markerLayer = new OpenLayers.Layer.Vector("Overlay");

var marker = new OpenLayers.Feature.Vector(
        new OpenLayers.Geometry.Point(longitude, latitude).transform(epsg4326, mercator),
        {
        	description:'Free University of Bozen - Bolzano'
        },
        {
        	externalGraphic: 'https://cdn2.iconfinder.com/data/icons/snipicons/500/map-marker-128.png',
        	graphicHeight: 35,
        	graphicWidth: 35,
        	graphicXOffset:-17,
        	graphicYOffset:-17  
        }
    );

markerLayer.addFeatures(marker);

map.addLayer(markerLayer);