        //highchart settings for download/print button added by Dominique
                        Highcharts.setOptions({
                                // added by Dominique to customize the export/print button in each chart (top-right corner)
                                navigation: {
                                        buttonOptions: {
                                            theme: {
                                                // Good old text links
                                                style: {
                                                    color: '#039',
                                                    textDecoration: 'underline'
                                                },
                                                title: _highcharts_download_button_title
                                            }
                                        }
                                    },
                                exporting: {
                                    buttons: {
                                        contextButton: {
                                                enabled: false
                                                },
                                                exportButton: {
                                                    
                                                    text: 'Download',
                                                    // Use only the download related menu items from the default context button
                                                    //menuItems: Highcharts.getOptions().exporting.buttons.contextButton.menuItems.splice(2)
                                                    menuItems: [{
                                			//Enable this section to include direct printing of charts in the dropdown menu of this button
                                			//If you choose to do so, rename the button to "Print/Download"	
                                //				textKey: 'printChart',
                                //				onclick: function () {
                                //					this.print();
                                //				}
                                //			}, {
                                //				separator: true
                                //			}, {
                                                    textKey: 'downloadPNG',
                                                    onclick: function () {
                                                            this.exportChart();
                                                        }
                                                     }, {
                                                    textKey: 'downloadJPEG',
                                                    onclick: function () {
                                                            this.exportChart({
                                                                    type: 'image/jpeg'
                                                                    });
                                                            }
                                                    }, {
                                                    textKey: 'downloadPDF',
                                                    onclick: function () {
                                                            this.exportChart({
                                                                    type: 'application/pdf'
                                                                    });
                                                            }
                                                    }
                                                    ]
                                                }
                                      }
                                 }    
                      });