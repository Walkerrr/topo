/**
 * 节点管理界面
 */
var expandState = false;



var levelSelected = false;
var typeSelected = false;
var topo_type = '';
var topo_level = '';
var index = '';
var selectedType = '';
var selectedLevel = '';
var options = [];
var nodes = [];
var provinceid = '';
var cityid = '';
var topo_id = '';
var showoptions = [];
var count = 0;
descr = '';

Array.prototype.indexOf = function(val) {
	for (var i = 0; i < this.length; i++) {
		if (this[i] == val) return i;
	}
	return -1;
};


Array.prototype.remove = function(val) {
	var index = this.indexOf(val);
	if (index > -1) {
		this.splice(index, 1);
	}
};

Array.prototype.del = function(index) {
	if (isNaN(index) || index >= this.length) {
		return false;
	}
	for (var i = 0, n = 0; i < this.length; i++) {
		if (this[i] != this[index]) {
			this[n++] = this[i];
		}
	}
	this.length -= 1;
};

$(function() {

	$('#container').panel({

		width: 1100,
		height: 500,
		style: {
			position: 'absolute',
			left: 132,
			top: 25
		}

	});

	$('#topo-def').treegrid({

		width: 264,
		height: 360,
		url: "./../ajax.php?c=CommTree&a=areaTree&maxlevel=3",
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
			expandState = true;

		},

		onCollapse: function(row) {

			expandState = false;

		},

		onClickRow: function(row) {

			if (row.text === '全国') {
				topo_id = '00';
				selectedLevel = '00';
			} else if (row.cityid === '*') {
				topo_id = row.provinceid;
				selectedLevel = '01';
			} else {
				topo_id = row.cityid;
				selectedLevel = '02'
			}
			if (cityid === '*') {
				$('#nodes-list').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllAvailWithoutCityId&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&provinceid=' + provinceid + '&topo_id=' + topo_id);
			} else {
				$('#nodes-list').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllAvail&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&provinceid=' + provinceid + '&cityid=' + cityid + '&topo_id=' + topo_id);
			}

			//点击一行传进参数把内容加载到中间
			$('#topo-nodes').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllTopoNodes&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&topo_id=' + topo_id);

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

	/*$('#topo-level').combobox({

		valueField: 'id',
		textField: 'topo_level',
		editable: false,
		data: [{
			topo_level: '全国拓扑',
			id: 00
		}, {
			topo_level: '省级拓扑',
			id: 01
		}, {
			topo_level: '本地网拓扑',
			id: 02
		}],

		onSelect: function(record) {

			levelSelected = true;

			$('#chosen-level').html(record.topo_level);
			topo_level = record.topo_level;

			if (typeSelected) {
				$('#topo-def').datagrid('load', '../ajax.php?c=TopoDefCtrl&a=getAllByTypeAndLevel&topo_type=' + topo_type + '&topo_level=' + topo_level);
			} else {
				$('#topo-def').datagrid('load', '../ajax.php?c=TopoDefCtrl&a=getAllByLevel&topo_level=' + topo_level);
			}
		},

		onLoadSuccess: function() {

			var html = $('#topo-level').combobox('getText');
			$('#chosen-level').html(html);

		}

	});*/

	$('#topo-type').combobox({

		url: '../ajax.php?c=TopoDefCtrl&a=getAllType',
		valueField: 'topo_type',
		textField: 'descr',
		editable: false,

		onSelect: function(record) {

			typeSelected = true;
			// descr = record.descr;
			// $('#chosen-type').html(record.descr);
			selectedType = record.topo_type;
			$('#topo-type').combobox('setText', record.descr)

			//点击一行传进参数把内容加载到中间
			if (selectedLevel) {
				$('#topo-nodes').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllTopoNodes&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&topo_id=' + topo_id);
			}

			if (cityid === '*') {
				$('#nodes-list').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllAvailWithoutCityId&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&provinceid=' + provinceid + '&topo_id=' + topo_id);
			} else {
				$('#nodes-list').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllAvail&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&provinceid=' + provinceid + '&cityid=' + cityid + '&topo_id=' + topo_id);
			}

		},

		onLoadSuccess: function() {

			selectedType = $('#topo-type').combobox('getData')[0].topo_type;
			$('#topo-type').combobox('setText', $('#topo-type').combobox('getData')[0].descr);

		}

	});

	/*$('#topo-def').treegrid({

		singleSelect: true,
		url: '../ajax.php?c=TopoDefCtrl&a=getAllTopoDef',
		striped: true,
		width: 320,
		height: 360,
		style: {
			position: 'absolute',
			left: 26,
		},

		onClickRow: function(index, row) {

			selectedLevel = row.topo_level;
			selectedType = row.topo_type;
			$('#selected-nodes-list').empty();
			$('#topo-nodes').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllTopoNodes&topo_type=' + row.topo_type + '&topo_level=' + row.topo_level);

		},

		onSelectAll: function(rows) {

			for (i = 0; i < rows.length; i++) {
				options.push(rows[i].node_id);
			}

		},

		onUnselectAll: function(rows) {

			options = [];

		}

	});*/

	/*$('#node-list').datagrid({

		onClickRow: function(index, row) {

			selectedLevel = row.topo_level;
			selectedType = row.topo_type;
			$('#selected-nodes-list').empty();
			$('#topo-nodes').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllTopoNodes&topo_type=' + row.topo_type + '&topo_level=' + row.topo_level + '&topo_id=' + topo_id);

		},

		onSelectAll: function(rows) {

			for (i = 0; i < rows.length; i++) {
				options.push(rows[i].node_id);
			}

		},

		onUnselectAll: function(rows) {

			options = [];

		}

	})
*/
	//中间拓扑节点列表
	$('#topo-nodes').datagrid({

		striped: true,
		width: 320,
		height: 360,
		style: {
			position: 'absolute',
			left: 392,
			top: 139
		},

		columns: [
			[{
					field: 'topo_id',
					title: '拓扑id',
					checkbox: true
				}, {
					field: 'node_name',
					title: '节点名称',
					width: 270,
					align: 'center'
				}, {
					field: 'gis_x',
					title: 'gis横坐标',
					width: 90,
					align: 'center',
					hidden: true
				}, {
					field: 'gis_y',
					title: 'gis纵坐标',
					width: 90,
					align: 'center',
					hidden: true
				}
				/*{field:'x',title:'逻辑坐标x',width:63,align:'center'} ,
				{field:'y',title:'逻辑坐标y',width:63,align:'center'} */
			]
		],

		onSelect: function(index, row) {

			//维护一个数组。将选项放进数组中。
			if ($.inArray(row.node_id, options) === -1) {
				options.push(row.node_id);
			}
			for (i = 0; i < 6; i++) {
				showoptions[i] = options[i];
			}
			if (count >= 6) {
				showoptions[7] = '...';
			}
			var html = showoptions.join(" ");
			$('#selected-nodes-list').html(html);

			count++;
			//$('#topo-nodes').datagrid('load','../ajax.php?c=TopoNodeCtrl&a=getAllTopoNodes&topo_type='+row.topo_type+'&topo_level='+row.topo_level);

		},

		onUnselect: function(index, row) {

			options.remove(row.node_id);

			if (count <= 6) {
				showoptions.remove("...");
				for (i = 0; i < 6; i++) {
					showoptions[i] = options[i];
				}
			}
			var html = showoptions.join(" ");
			$('#selected-nodes-list').html(html);

			count--;

		},

		onSelectAll: function(rows) {

			count = rows.length;

			for (i = 0; i < rows.length; i++) {
				options.push(rows[i].node_id);
			}

			for (i = 0; i < 6; i++) {
				showoptions[i] = options[i];
			}
			if (count >= 6) {
				showoptions[7] = '...';
			}
			var html = showoptions.join(" ");
			$('#selected-nodes-list').html(html);

			showoptions = [];

		},

		onUnselectAll: function(rows) {

			count = 0;

			options = [];

			$('#selected-nodes-list').html("");

		}

	});

	$('#add-btn').linkbutton({

		width: 42,
		height: 30,
		iconCls: 'icon-left-arrow',
		plain: true,

		onClick: function() {

			$.ajax({

				type: 'post',
				url: '../ajax.php',
				data: {
					'c': 'TopoNodeCtrl',
					'a': 'addTopoNode',
					'topo_type': selectedType,
					'topo_level': selectedLevel,
					'nodes': nodes
				},
				dataType: 'json',
				success: function(data) {

					options = [];
					nodes = [];
					$('#topo-nodes').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllTopoNodes&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&topo_id=' + topo_id);
					if (cityid === '*') {
						$('#nodes-list').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllAvailWithoutCityId&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&provinceid=' + provinceid + '&topo_id=' + topo_id);
					} else {
						$('#nodes-list').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllAvail&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&provinceid=' + provinceid + '&cityid=' + cityid + '&topo_id=' + topo_id);
					}

				},
				error: function(data) {



				}

			});

		}

	});

	$('#showAll').linkbutton({

		iconCls: 'icon-add',

		onClick: function() {

			$('#topo-def').datagrid('load', '../ajax.php?c=TopoDefCtrl&a=getAllTopoDef');

		}

	});

	$('#remove-btn').linkbutton({

		width: 42,
		height: 30,
		iconCls: 'icon-right-arrow',
		plain: true,

		onClick: function() {

			$.ajax({

				type: 'post',
				url: '../ajax.php',
				data: {
					'c': 'TopoNodeCtrl',
					'a': 'delTopoNode',
					'topo_type': selectedType,
					'topo_level': selectedLevel,
					'topo_id': topo_id,
					'nodes': options
				},
				dataType: 'json',
				success: function(data) {

					$('#topo-nodes').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllTopoNodes&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&topo_id=' + topo_id);
					$('#selected-nodes-list').empty();
					options = [];
					nodes = [];
					if (cityid === '*') {
						$('#nodes-list').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllAvailWithoutCityId&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&provinceid=' + provinceid + '&topo_id=' + topo_id);
					} else {
						$('#nodes-list').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllAvail&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&provinceid=' + provinceid + '&cityid=' + cityid + '&topo_id=' + topo_id);
					}

				}

			});

		}

	});

	$('#add-nodes-btn').linkbutton({

		plain: true,
		width: 180,
		height: 40,
		iconCls: 'icon-add',

		onClick: function() {

			$('#regiontreeKey').treegrid("collapseAll");

			var rs = new RegionSelectWin('area_id');

			rs.initRegion('', function(data) {

				provinceid = data.provinceid;
				cityid = data.cityid;
				if (selectedType) {
					if (cityid === '*') {
						$('#nodes-list').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllAvailWithoutCityId&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&provinceid=' + provinceid + '&topo_id=' + topo_id);
					} else {
						$('#nodes-list').datagrid('load', '../ajax.php?c=TopoNodeCtrl&a=getAllAvail&topo_type=' + selectedType + '&topo_level=' + selectedLevel + '&provinceid=' + provinceid + '&cityid=' + cityid + '&topo_id=' + topo_id);
					}
				} else {
					$.messager.alert('Warning', '请选择拓扑类型后再进行添加！');
				}
			});

			rs.openRegionWin(560, 100);

		}

	});

	$('#nodes-list').datagrid({

		striped: true,
		width: 320,
		height: 360,
		style: {
			position: 'absolute',
			left: 758,
			top: 139
		},
		columns: [
			[{
				field: 'node_id',
				title: '节点id',
				checkbox: true
			}, {
				field: 'node_name',
				title: '节点名称',
				width: 120,
				align: 'center'
			}, {
				field: 'node_type',
				title: '节点级别',
				width: 120,
				align: 'center'
			}, {
				field: 'node_longitude',
				title: 'gis横坐标',
				width: 67,
				align: 'center',
				hidden: true
			}, {
				field: 'node_latitude',
				title: 'gis纵坐标',
				width: 67,
				align: 'center',
				hidden: true
			}, {
				field: 'provinceid',
				title: 'provinceid',
				hidden: true
			}, {
				field: 'cityid',
				title: 'cityid',
				hidden: true
			}, {
				field: 'x',
				title: 'x',
				hidden: true
			}, {
				field: 'y',
				title: 'y',
				hidden: true
			}, {
				field: 'orderindex',
				title: '排序下标',
				hidden: true
			}]
		],

		onSelect: function(index, row) {

			/*if (selectedLevel === '全国拓扑') {
				topo_id = 00;
			} else if (selectedLevel === '省级拓扑') {
				topo_id = row.provinceid;
			} else if (selectedLevel === '本地网拓扑') {
				topo_id = row.cityid;
			}*/
			//维护一个数组。将节点对象放进数组中。
			var node = {
				"node_id": row.node_id,
				"gis_x": row.node_longitude,
				"gis_y": row.node_latitude,
				"x": row.x,
				"y": row.y,
				"topo_id": topo_id,
				"orderindex": row.orderindex
			}

			nodes.push(node);
			//$('#topo-nodes').datagrid('load','../ajax.php?c=TopoNodeCtrl&a=getAllTopoNodes&topo_type='+row.topo_type+'&topo_level='+row.topo_level);

		},

		onUnselect: function(index, row) {

			for (i = 0; i < nodes.length; i++) {
				if (nodes[i].node_id === row.node_id) {
					nodes.del(i);
				}
			}

		},

		onSelectAll: function(rows) {

			for (i = 0; i < rows.length; i++) {
				var row = rows[i];
				var node = {
					"node_id": row.node_id,
					"gis_x": row.node_longitude,
					"gis_y": row.node_latitude,
					"x": row.x,
					"y": row.y,
					"topo_id": topo_id,
					"orderindex": row.orderindex
				}
				nodes.push(node);
			}

		},

		onUnselectAll: function(rows) {

			nodes = [];

		}

	});

	//默认加载全国第一个类型的数据 node-list

})