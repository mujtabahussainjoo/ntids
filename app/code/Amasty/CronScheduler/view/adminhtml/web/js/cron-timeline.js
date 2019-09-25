define([
    'jquery',
    'Amasty_CronScheduler/vendor/vis/vis-min',
    'jquery/ui'
], function ($, vis) {
    'use strict';

    $.widget('amasty.cronTimeline', {
        options: {
            data: null,
            storeUtcOffset: null
        },

        _create: function() {
            var self = this,
                cronJobCodes = [],
                cronJobCodesFlags = [],
                cronData = this.options.data,
                countOfCronRecords = cronData.length,
                cronTimelineGroups = new vis.DataSet(),
                cronTimelineRecords = new vis.DataSet(),
                cronTimelineOptions = {
                    width: '100%',
                    height: '800px',
                    orientation: 'top',
                    stack: false,
                    verticalScroll: true,
                    zoomKey: 'ctrlKey',
                    tooltip: {
                        followMouse: true,
                        overflowMethod: 'cap'
                    },
                    start: new Date((new Date()).valueOf() - 1000*60*60*3),
                    end: new Date((new Date()).valueOf() + 1000*60*60*1),
                    moment: function (date) {
                        return vis.moment(date).utcOffset(self.options.storeUtcOffset);
                    }
                };

            for (var index = 0; index < countOfCronRecords; index++) {
                if (cronJobCodesFlags[cronData[index].job_code]) continue;
                cronJobCodesFlags[cronData[index].job_code] = true;
                cronJobCodes.push(cronData[index].job_code);
            }

            for (var index = 0; index < cronJobCodes.length; index++) {
                cronTimelineGroups.add({
                    id: cronJobCodes[index],
                    content: cronJobCodes[index]
                });
            }

            cronTimelineRecords.add(this.generateRecordsData(cronData));

            var timeline = new vis.Timeline(document.getElementById('cron-timeline'),
                cronTimelineRecords, cronTimelineGroups, cronTimelineOptions);
            var count;
			for(count =0; count<=4; count++)
			{
				timeline.zoomIn(0.4);
			}
            $('[data-amcronsch-js="timeline-zoom-in"]').on('click', function () {
                timeline.zoomIn(0.4);
            });

            $('[data-amcronsch-js="timeline-zoom-out"]').on('click', function () {
                timeline.zoomOut(0.4);
            });
        },

        generateRecordsData: function (cronRecords) {
            var self = this,
                recordsForTimeline = cronRecords.map(function (record) {
                return {
                    'start': (['error', 'success', 'run'].indexOf(record.status) != -1)
                        ? new Date(record.executed_at.replace(' ', 'T') + 'Z')
                        : new Date(record.scheduled_at.replace(' ', 'T') + 'Z'),
                    'end': (['error', 'success'].indexOf(record.status) != -1)
                        ? new Date(record.executed_at.replace(' ', 'T') + 'Z')
                        : new Date(record.scheduled_at.replace(' ', 'T') + 'Z'),
                    'className': 'amcronsch-record -' + record.status,
                    'group': record.job_code,
                    'title': self.renderRecordInfo(record),
                    'type': 'range'
                };
            });

            return recordsForTimeline;
        },

        renderRecordInfo: function (cronRecord) {
            var recordInfoContainer = '',
                recordInfoContent = '';

            $.each(cronRecord, function(key, value) {
                recordInfoContent +=
                    '<tr class="amcronsch-' + key +'">' +
                    '    <td class="amcronsch-cell amcronsch-label">' + key + '</td>' +
                    '    <td class="amcronsch-cell" ><span class="amcronsch-value' + ((key == 'status') ? (' -' + value) : '') + '">' + value + '</span></td>' +
                    '</tr>'
            });

            recordInfoContainer =
                '<div class="amcronsch-record-info">' +
                '   <table>' +
                '   <thead><th class="amcronsch-cell amcronsch-header" colspan="2"><span class="amcronsch-type">' + cronRecord['job_code'] + '</span></th></thead>' +
                recordInfoContent +
                '   </table>' +
                '   </div>';

            return recordInfoContainer;
        }
    });

    return $.amasty.cronTimeline;
});
