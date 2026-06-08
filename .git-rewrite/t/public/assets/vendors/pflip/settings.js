var option_PDFF = {
 
   /* BASIC SETTINGS */  
    openPage: 1,
    height: '100%',
    enableSound: true,
    downloadEnable: true, 
    direction: pdfflip.DIRECTION.LTR,
    autoPlay: true,
    autoPlayStart: false,
    autoPlayDuration: 3000,
    autoEnableOutline: false,
    autoEnableThumbnail: false,


	/* TRANSLATE INTERFACE */  
 
    text: {
      toggleSound: "Sound",
      toggleThumbnails: "Thumbnails",
      toggleOutline: "Contents",
      previousPage: "Previous Page",
      nextPage: "Next Page",
      toggleFullscreen: "Fullscreen",
      zoomIn: "Zoom In",
      zoomOut: "Zoom Out",
      downloadPDFFile: "Download PDF",
      gotoFirstPage: "First Page",
      gotoLastPage: "Last Page",
      play: "AutoPlay On",
      pause: "AutoPlay Off",
      share: "Share"
    },

	/* ADVANCED SETTINGS */  

    hard: "none",
    overwritePDFOutline: true,
    duration: 1000,
pageMode: pdfflip.PAGE_MODE.DOUBLE,
singlePageMode: pdfflip.SINGLE_PAGE_MODE.BOOKLET,
	transparent: false,
	scrollWheel: true,
    zoomRatio: 1.5,
	maxTextureSize: 1600,
	backgroundImage: "background.jpg",
    backgroundColor: "#fff",
    controlsPosition: pdfflip.CONTROLSPOSITION.BOTTOM,
    allControls: "thumbnail,play,altPrev,pageNumber,altNext,zoomIn,zoomOut,fullScreen,download,sound,share",
    hideControls: "outline,startPage,endPage",

};

// var pdfflipLocation = "./pflip/";