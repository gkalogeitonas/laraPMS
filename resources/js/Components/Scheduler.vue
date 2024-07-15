<template>
  <DayPilotScheduler :config="config" ref="schedulerRef" />
</template>

<script setup>
import { DayPilot, DayPilotScheduler } from 'daypilot-pro-vue'
import { ref, reactive, onMounted } from 'vue'

const config = reactive({
  timeHeaders: [{ groupBy: 'Month' }, { groupBy: 'Day', format: 'd' }],
  scale: 'Day',
  days: DayPilot.Date.today().daysInYear(),
  startDate: DayPilot.Date.today().firstDayOfYear(),
  timeRangeSelectedHandling: 'Enabled',
  eventHeight: 40,
  onTimeRangeSelected: async (args) => {
    const dp = schedulerRef.value?.control
    const modal = await DayPilot.Modal.prompt('Create a new event:', 'Event 1')
    dp.clearSelection()
    if (modal.canceled) {
      return
    }
    dp.events.add({
      start: args.start,
      end: args.end,
      id: DayPilot.guid(),
      resource: args.resource,
      text: modal.result
    })
  },
  eventMoveHandling: 'Update',
  onEventMoved: (args) => {
    args.control.message('Event moved: ' + args.e.data.text)
  },
  eventResizeHandling: 'Update',
  onEventResized: (args) => {
    args.control.message('Event resized: ' + args.e.data.text)
  },
  eventClickHandling: 'Enabled',
  onEventClicked: (args) => {
    args.control.message('Event clicked: ' + args.e.data.text)
  },
  eventHoverHandling: 'Disabled',
  treeEnabled: true,
  onBeforeEventRender: (args) => {
    args.data.barColor = args.data.color
    args.data.areas = [
      {
        top: 12,
        right: 6,
        width: 20,
        height: 20,
        symbol: 'icons/daypilot.svg#minichevron-down-4',
        fontColor: '#999',
        visibility: 'Hover',
        action: 'ContextMenu',
        padding: 1,
        style:
          'background-color: #f9f9f9; border: 2px solid #ccc; cursor:pointer; border-radius: 50%;'
      }
    ]
  },
  contextMenu: new DayPilot.Menu({
    items: [
      {
        text: 'Delete',
        onClick: (args) => {
          const e = args.source
          const dp = schedulerRef.value?.control
          dp.events.remove(e)
          dp.message('Deleted.')
        }
      },
      {
        text: '-'
      },
      {
        text: 'Blue',
        icon: 'icon icon-blue',
        color: '#1155cc',
        onClick: (args) => {
          updateColor(args.source, args.item.color)
        }
      },
      {
        text: 'Green',
        icon: 'icon icon-green',
        color: '#6aa84f',
        onClick: (args) => {
          updateColor(args.source, args.item.color)
        }
      },
      {
        text: 'Yellow',
        icon: 'icon icon-yellow',
        color: '#f1c232',
        onClick: (args) => {
          updateColor(args.source, args.item.color)
        }
      },
      {
        text: 'Red',
        icon: 'icon icon-red',
        color: '#cc0000',
        onClick: (args) => {
          updateColor(args.source, args.item.color)
        }
      }
    ]
  })
})
const schedulerRef = ref(null)

const loadReservations = () => {
  const events = [
    {
      id: 1,
      resource: 'R1',
      start: DayPilot.Date.today().firstDayOfMonth().addDays(3),
      end: DayPilot.Date.today().firstDayOfMonth().addDays(7),
      text: 'Event 1',
      color: '#1155cc'
    },
    {
      id: 2,
      resource: 'R1',
      start: DayPilot.Date.today().firstDayOfMonth().addDays(9),
      end: DayPilot.Date.today().firstDayOfMonth().addDays(12),
      text: 'Event 2',
      color: '#6aa84f'
    },
    {
      id: 3,
      resource: 'R3',
      start: DayPilot.Date.today().firstDayOfMonth().addDays(3),
      end: DayPilot.Date.today().firstDayOfMonth().addDays(5),
      text: 'Event 3',
      color: '#1155cc'
    },
    {
      id: 4,
      resource: 'R3',
      start: DayPilot.Date.today().firstDayOfMonth().addDays(5),
      end: DayPilot.Date.today().firstDayOfMonth().addDays(7),
      text: 'Event 4',
      color: '#6aa84f'
    },
    {
      id: 5,
      resource: 'R3',
      start: DayPilot.Date.today().firstDayOfMonth().addDays(7),
      end: DayPilot.Date.today().firstDayOfMonth().addDays(9),
      text: 'Event 5',
      color: '#f1c232'
    },
    {
      id: 6,
      resource: 'R10',
      start: '2024-07-19T13:00:00',
      end: '2024-07-25T12:00:00',
      text: 'Event 68',
      color: '#cc0000'
    },
    {
      id: 7,
      resource: 'R10',
      start: '2024-07-08T00:00:00',
      end: '2024-07-19T12:00:00',
      text: 'Event 6',
      color: '#cc0000'
    }
  ]
  config.events = events
}

const loadResources = () => {
  const resources = [
    {
      name: 'Group A',
      id: 'GA',
      expanded: true,
      children: [
        { name: 'Resource 1', id: 'R1' },
        { name: 'Resource 2', id: 'R2' },
        { name: 'Resource 3', id: 'R3' },
        { name: 'Resource 4', id: 'R4' },
        { name: 'Resource 10', id: 'R10' }
      ]
    },
    {
      name: 'Group B',
      id: 'GB',
      expanded: true,
      children: [
        { name: 'Resource 5', id: 'R5' },
        { name: 'Resource 6', id: 'R6' },
        { name: 'Resource 7', id: 'R7' },
        { name: 'Resource 8', id: 'R8' }
      ]
    }
  ]
  config.resources = resources
}

const updateColor = (e, color) => {
  const dp = schedulerRef.value?.control
  e.data.color = color
  dp.events.update(e)
  dp.message('Color updated')
}

onMounted(() => {
  const dp = schedulerRef.value?.control

  loadResources()
  loadReservations()

  dp.message('Welcome!')
  dp.scrollTo(DayPilot.Date.today().firstDayOfMonth())
})
</script>
