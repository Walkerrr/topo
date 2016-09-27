//拓扑定义的树型展示。


$(function() {

	$('#topo-def').treegrid({

		width: 264,
		height: 360,
		url: "./../ajax.php?c=CommTree&a=areaTree&id=00&maxlevel=3",
		method: 'post',
		singleSelect: true,
		style: {
			position: 'absolute',
			left: 51
		},
		lines: true,
		rownumbers: false,
		idField: 'id',
		treeField: 'text',
		singleSelect: true,
		columns: [
			[{
				"field": "text",
				"title": "地区拓扑管理",
				"width": 248,
				"align": "left"
			}, {
				"field": "id",
				"title": "区域编码",
				"width": 0,
				"align": "center",
				"hidden": true
			}]
		],

		onBeforeExpand: function() {

			$('#regiontreeKey').treegrid("collapseAll");

		},

		onExpand: function(row) {

			var id = row.id;
			$('#province').html(row.text);
			expandState = true;

		},

		onCollapse: function(row) {

			expandState = false;

		},

		onClickRow: function(row) {

			if (row.cityid === '*') {
				thisNodeId = row.provinceid;
			} else {
				thisNodeId = row.cityid;
			}

		}

		/*onDblClickRow: function(row) {

			if (expandState) {
				if (row.cityid === "*") {
					$('#province').html(row.text);
					$('#city').html("*");
				} else {
					$('#city').html(row.text);
				}
			} else {
				$('#province').html(row.text);
				$('#city').html("*");
			}
			var id = row.id;
			that.key_areaobj.provinceid = row.provinceid;
			that.key_areaobj.cityid = row.cityid;
			that.key_areaobj.conrgnid = row.conrgnid;
			that.key_areaobj.intesrvrgnid = row.intesrvrgnid;

			if (callback) {
				callback.call(this, that.key_areaobj);
			}
		}*/

	});

})