from jung import Jung
import asyncio
import json


async def handle_request(reader, writer):
    data = await reader.read(256)

    if not data:
        response = {
            'code': 400,
            'error': 'no data sent',
            'data': None
        }
    else:
        search = data.decode()
        print("searching for: " + search)
        j = Jung()
        response = {
            'code': 200,
            'error': None,
            'data': j.search(search)
        }

    json_response = json.dumps(response);
    print('sending: ' + json_response)
    writer.write(json_response.encode())
    await writer.drain()
    writer.close()


async def main():
    server = await asyncio.start_server(
        handle_request, '127.0.0.1', 1994)

    addr = server.sockets[0].getsockname()
    print(f'Serving on {addr}')

    async with server:
        await server.serve_forever()

asyncio.run(main())
