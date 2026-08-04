/*
* Extended datatable library function
* Author: Raheel Khan
* Version: 1.0
*/

function my_datatable(datatable, config) {
	if (!datatable) return;
	config = config || {};

	if (!datatable.hasOwnProperty('selector') || !datatable.hasOwnProperty('fnSettings')) return;

	var c = {
		exportable: false,
		export_btn_text: 'Export',
		source: datatable.fnSettings().sAjaxSource,
		headers: [],
		file_name: '',
		export_type: ['csv'],
		event_id: null,
	};
	$.extend(c, config);


	// enable export button
	if (c.exportable) {
		var csv_headers = '';
		if (c.headers && c.headers.length > 0) {
			for (var h = 0; h < c.headers.length; h++) {
				csv_headers += c.headers[h] + ',';
			}
		} else {
			$(datatable.selector + '_wrapper .dataTables_scrollHeadInner thead tr th').each(function (e) {
				if ($(this).text() == '#') {
					csv_headers += 'S.no,';
				} else {
					csv_headers += $(this).text() + ',';
				}
			});
		}

		var query_string = (c.source.indexOf('?') >= 0) ? '&' : '?';
		query_string += 'headers=' + encodeURIComponent(csv_headers);

		if (datatable.fnSettings && datatable.fnSettings().aoColumns && datatable.fnSettings().aoColumns.length > 0) {
			var col_map = [];
			for (var i = 0; i < datatable.fnSettings().aoColumns.length; i++) {
				var m = datatable.fnSettings().aoColumns[i].mData;
				if (typeof m !== 'undefined' && m !== null) {
					col_map.push(m);
				}
			}
			if (col_map.length > 0) {
				query_string += '&col_map=' + encodeURIComponent(col_map.join(','));
			}
		}

		if (c.file_name != '') {
			query_string += '&file_name=' + encodeURIComponent(c.file_name);
		}
		if (c.event_id != null) {
			query_string += '&event_id=' + encodeURIComponent(c.event_id);
		}

		if (c.export_type.length > 0) {
			for (var e = 0; e < c.export_type.length; e++) {
				var btn_text = c.export_btn_text + ' ' + c.export_type[e];
				query_string = query_string += '&export_type=' + c.export_type[e];
				$(datatable.selector + '_wrapper .dataTables_filter')
					.append('<a href="' + c.source + query_string + '" target="_blank" class="btn btn-default btn-sm" style="padding: 3px;vertical-align: top;margin-left: 5px;display: inline-block;">' + btn_text + '</a>');
			}
		} else {
			$(datatable.selector + '_wrapper .dataTables_filter')
				.append('<a href="' + c.source + query_string + '" target="_blank" class="btn btn-default btn-sm" style="padding: 3px 12px;vertical-align: top;margin-left: 5px;display: inline-block;">' + c.export_btn_text + '</a>');
		}
	}
}